<?php

namespace App\Console\Commands;

use App\Enums\StatutCandidature;
use App\Models\Candidature;
use Illuminate\Console\Command;

class FixDesyncStatuts extends Command
{
    protected $signature = 'fix:desync-statuts {--id= : ID spécifique de la candidature à corriger} {--dry-run : Afficher les corrections sans les appliquer}';
    protected $description = 'Détecte et corrige les candidatures dont le statut est désynchronisé par rapport aux données saisies';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $specificId = $this->option('id');

        if ($dryRun) {
            $this->warn('🔍 Mode dry-run : aucune modification ne sera effectuée.');
        }

        $query = Candidature::query();
        if ($specificId) {
            $query->where('id', $specificId);
        }

        $candidatures = $query->get();
        $fixCount = 0;

        foreach ($candidatures as $candidature) {
            $currentStatut = $candidature->statut;
            $targetStatut = $this->detectTargetStatut($candidature);

            if (!$targetStatut || $targetStatut === $currentStatut) {
                continue;
            }

            if ($targetStatut->getEtape() <= $currentStatut->getEtape()) {
                continue;
            }

            $this->info("📌 Candidature #{$candidature->id} ({$candidature->nom} {$candidature->prenom})");
            $this->line("   Statut actuel : {$currentStatut->getLabel()} (étape {$currentStatut->getEtape()}/13)");
            $this->line("   Statut cible  : {$targetStatut->getLabel()} (étape {$targetStatut->getEtape()}/13)");

            // Construire le chemin de transitions
            $path = $this->buildTransitionPath($currentStatut, $targetStatut);

            if (empty($path)) {
                $this->error("   ⚠️  Aucun chemin de transition trouvé !");
                continue;
            }

            $pathLabels = collect($path)->map(fn ($s) => $s->getLabel())->implode(' → ');
            $this->line("   Chemin : {$currentStatut->getLabel()} → {$pathLabels}");

            if (!$dryRun) {
                // Enregistrer l'historique des transitions
                $historique = $candidature->historique_statuts ?? [];
                $previousStatut = $currentStatut;
                foreach ($path as $step) {
                    $historique[] = [
                        'de' => $previousStatut->value,
                        'vers' => $step->value,
                        'date' => now()->toIso8601String(),
                        'utilisateur' => 'Système (fix:desync-statuts)',
                        'commentaire' => 'Correction automatique de désynchronisation',
                    ];
                    $previousStatut = $step;
                }

                Candidature::withoutEvents(function () use ($candidature, $targetStatut, $historique) {
                    $candidature->update([
                        'statut' => $targetStatut->value,
                        'historique_statuts' => $historique,
                    ]);
                });

                $this->info("   ✅ Corrigé → {$targetStatut->getLabel()}");
            } else {
                $this->comment("   (dry-run, non appliqué)");
            }

            $fixCount++;
        }

        if ($fixCount === 0) {
            $this->info('✅ Aucune désynchronisation détectée.');
        } else {
            $verb = $dryRun ? 'à corriger' : 'corrigée(s)';
            $this->newLine();
            $this->info("📊 {$fixCount} candidature(s) {$verb}.");
        }

        return 0;
    }

    private function detectTargetStatut(Candidature $record): ?StatutCandidature
    {
        if ($record->remboursement_effectue && $record->date_remboursement) {
            return StatutCandidature::TERMINE;
        }
        if ($record->attestation_generee || $record->chemin_attestation) {
            return StatutCandidature::ATTESTATION_GENEREE;
        }
        if ($record->note_evaluation !== null && $record->note_evaluation > 0) {
            return StatutCandidature::EVALUATION_TERMINEE;
        }
        if ($record->date_evaluation && ($record->note_evaluation === null || $record->note_evaluation == 0)) {
            return StatutCandidature::EN_EVALUATION;
        }
        if ($record->induction_completee) {
            return StatutCandidature::INDUCTION_TERMINEE;
        }
        if ($record->date_induction && !$record->induction_completee) {
            return StatutCandidature::INDUCTION_PLANIFIEE;
        }
        if ($record->reponse_lettre_envoyee) {
            return StatutCandidature::REPONSE_LETTRE_ENVOYEE;
        }
        if ($record->service_affecte && $record->tuteur_id) {
            return StatutCandidature::AFFECTE;
        }
        if ($record->note_test !== null && $record->note_test > 0) {
            return StatutCandidature::TEST_PASSE;
        }
        if ($record->date_test && ($record->note_test === null || $record->note_test == 0)) {
            return StatutCandidature::TEST_PLANIFIE;
        }

        return null;
    }

    private function buildTransitionPath(StatutCandidature $from, StatutCandidature $to): array
    {
        if ($from === $to) {
            return [];
        }

        $queue = [[$from]];
        $visited = [$from->value => true];

        while (!empty($queue)) {
            $path = array_shift($queue);
            $current = end($path);

            foreach ($current->getNextStatuts() as $next) {
                if (isset($visited[$next->value])) {
                    continue;
                }

                $newPath = array_merge($path, [$next]);

                if ($next === $to) {
                    array_shift($newPath);
                    return $newPath;
                }

                if (count($newPath) > 15) {
                    continue;
                }

                $visited[$next->value] = true;
                $queue[] = $newPath;
            }
        }

        return [];
    }
}
