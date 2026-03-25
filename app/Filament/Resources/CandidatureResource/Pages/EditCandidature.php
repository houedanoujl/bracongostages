<?php

namespace App\Filament\Resources\CandidatureResource\Pages;

use App\Filament\Resources\CandidatureResource;
use App\Enums\StatutCandidature;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\View\View;

class EditCandidature extends EditRecord
{
    protected static string $resource = CandidatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('retour_liste')
                ->label('← Retour à la liste')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => CandidatureResource::getUrl('index')),
            Actions\ViewAction::make()
                ->label('Aperçu')
                ->icon('heroicon-o-eye')
                ->color('info'),
        ];
    }

    /**
     * Masquer les actions par défaut du formulaire.
     * La progression et la sauvegarde sont gérées via la sidebar de chaque étape.
     */
    protected function getFormActions(): array
    {
        return [];
    }

    /**
     * Rendu du footer : barre de progression du workflow.
     */
    protected function getFooterWidgets(): array
    {
        return [];
    }

    public function getFooter(): ?\Illuminate\Contracts\View\View
    {
        $record = $this->record;
        if (!$record || !$record->statut) {
            return null;
        }

        $steps = CandidatureResource::getWizardStepNames();
        $currentWizardStep = self::getWizardStepForStatut($record->statut);

        return view('filament.components.workflow-progress-bar', [
            'record' => $record,
            'steps' => $steps,
            'currentStep' => $currentWizardStep,
        ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Rafraîchir le record pour avoir le statut le plus à jour (après auto-avancement)
        $this->record->refresh();

        if (isset($data['statut'])) {
            $currentStatut = $this->record->statut;
            $newStatut = StatutCandidature::tryFrom($data['statut']);

            if ($currentStatut && $newStatut && $currentStatut !== $newStatut) {
                // CAS 1 : Le formulaire tente de rétrograder le statut (étape inférieure ou égale)
                // Cela arrive quand l'auto-avancement a déjà fait progresser le statut en DB
                // mais le formulaire contient encore l'ancienne valeur → on garde le statut actuel
                if ($newStatut->getEtape() < $currentStatut->getEtape()) {
                    $data['statut'] = $currentStatut->value;

                    Notification::make()
                        ->title('ℹ️ Statut conservé')
                        ->body("Le statut a été automatiquement maintenu à « {$currentStatut->getLabel()} » (étape {$currentStatut->getEtape()}/13). Le formulaire contenait une valeur obsolète.")
                        ->info()
                        ->duration(5000)
                        ->send();

                    return $data;
                }

                // CAS 2 : Transition vers l'avant mais non autorisée
                if (!$currentStatut->canTransitionTo($newStatut)) {
                    $nextLabels = collect($currentStatut->getNextStatuts())
                        ->map(fn ($s) => $s->getLabel())
                        ->implode(', ');
                    $message = "Impossible de passer de \"{$currentStatut->getLabel()}\" à \"{$newStatut->getLabel()}\".";
                    if (!empty($nextLabels)) {
                        $message .= " Prochaine(s) étape(s) autorisée(s) : {$nextLabels}.";
                    }

                    Notification::make()
                        ->title('⛔ Transition de statut non autorisée')
                        ->body($message)
                        ->danger()
                        ->persistent()
                        ->send();

                    $this->halt();
                }
            }
        }

        return $data;
    }

    /**
     * Après la sauvegarde, auto-avancer le statut si des données clés sont remplies,
     * et reconstruire l'historique si le statut a changé.
     */
    protected function afterSave(): void
    {
        $record = $this->record->fresh();
        $originalStatut = $record->getOriginal('statut');

        // Normaliser : getOriginal peut retourner un enum (cast) ou une string
        $ancienStatut = $originalStatut instanceof StatutCandidature
            ? $originalStatut
            : StatutCandidature::tryFrom($originalStatut);

        // Si le statut a changé via le formulaire, reconstruire l'historique
        if ($ancienStatut && $record->statut !== $ancienStatut) {
            self::enregistrerHistorique($record, $ancienStatut, $record->statut, 'Modifié via le formulaire d\'édition');
        }

        // ====== AUTO-AVANCEMENT DU STATUT ======
        // Détecter si des données clés ont été remplies et avancer le statut automatiquement
        self::autoAdvanceStatut($record);
    }

    /**
     * Premier statut correspondant à une étape wizard donnée.
     * Utilisé pour l'avancement déterministe entre étapes.
     */
    public static function getFirstStatutForWizardStep(int $wizardStep): ?StatutCandidature
    {
        return match ($wizardStep) {
            4 => StatutCandidature::ANALYSE_DOSSIER,
            5 => StatutCandidature::ATTENTE_TEST,
            6 => StatutCandidature::TEST_PASSE,
            7 => StatutCandidature::PLANIFICATION,
            8 => StatutCandidature::REPONSE_LETTRE_ENVOYEE,
            9 => StatutCandidature::EN_EVALUATION,
            10 => StatutCandidature::ATTESTATION_GENEREE,
            11 => StatutCandidature::REMBOURSEMENT_EN_COURS,
            default => null,
        };
    }

    /**
     * Avancement déterministe vers l'étape SUIVANTE du wizard.
     * Appelé par le bouton "Sauvegarder et passer à".
     * Ne saute JAMAIS plus d'une étape wizard à la fois.
     */
    public static function advanceToNextWizardStep($record, int $currentStepNumber, string $stepName): void
    {
        $nextStepNumber = $currentStepNumber + 1;
        $targetStatut = self::getFirstStatutForWizardStep($nextStepNumber);

        if (!$targetStatut) {
            // Dernière étape : on essaie de terminer
            if ($record->statut !== StatutCandidature::TERMINE) {
                $path = self::buildTransitionPath($record->statut, StatutCandidature::TERMINE);
                if (!empty($path)) {
                    $previousStatut = $record->statut;
                    foreach ($path as $step) {
                        $record->statut = $step;
                        self::enregistrerHistorique($record, $previousStatut, $step, 'Terminaison via bouton étape ' . $stepName);
                        $previousStatut = $step;
                    }
                    $record->saveQuietly();
                }
            }
            return;
        }

        // Vérifier que le target est réellement plus avancé
        if ($targetStatut->getEtape() <= $record->statut->getEtape()) {
            return;
        }

        // Construire le chemin BFS vers le premier statut de l'étape suivante
        $path = self::buildTransitionPath($record->statut, $targetStatut);

        if (empty($path)) {
            // Si pas de chemin direct, essayer d'avancer d'un cran dans le workflow
            $nextStatuts = $record->statut->getNextStatuts();
            if (!empty($nextStatuts)) {
                $bestNext = null;
                foreach ($nextStatuts as $candidate) {
                    if ($candidate === StatutCandidature::REJETE) continue;
                    if ($bestNext === null || $candidate->getEtape() > $bestNext->getEtape()) {
                        $bestNext = $candidate;
                    }
                }
                if ($bestNext) {
                    $record->statut = $bestNext;
                    self::enregistrerHistorique($record, $record->getOriginal('statut') ?? $record->statut, $bestNext, 'Avancement via bouton étape ' . $stepName);
                    $record->saveQuietly();
                }
            }
            return;
        }

        // Appliquer le chemin de transitions
        $previousStatut = $record->statut;
        foreach ($path as $step) {
            $record->statut = $step;
            self::enregistrerHistorique($record, $previousStatut, $step, 'Avancement via bouton étape ' . $stepName);
            $previousStatut = $step;
        }
        $record->saveQuietly();
    }

    /**
     * Avance automatiquement le statut UNIQUEMENT au sein de la même étape wizard.
     * Par exemple : TEST_PLANIFIE → TEST_PASSE (les deux sont dans l'étape 5 "Tests").
     * Ne traverse JAMAIS les frontières entre étapes wizard.
     */
    public static function autoAdvanceStatut($record): void
    {
        $currentStatut = $record->statut;
        $currentWizardStep = self::getWizardStepForStatut($currentStatut);
        $targetStatut = self::detectTargetStatut($record);

        if (!$targetStatut || $targetStatut === $currentStatut) {
            return;
        }

        // Vérifier que le target est "plus avancé" que le statut actuel
        if ($targetStatut->getEtape() <= $currentStatut->getEtape()) {
            return;
        }

        // === GARDE : ne jamais traverser les frontières entre étapes wizard ===
        $targetWizardStep = self::getWizardStepForStatut($targetStatut);
        if ($targetWizardStep > $currentWizardStep) {
            // Le target est dans une étape wizard ultérieure → on refuse le saut
            // On cherche le statut le plus avancé DANS la même étape wizard
            $targetStatut = self::detectTargetStatutWithinStep($record, $currentWizardStep);
            if (!$targetStatut || $targetStatut === $currentStatut) {
                return;
            }
        }

        // Construire le chemin de transitions pour atteindre le target
        $path = self::buildTransitionPath($currentStatut, $targetStatut);

        if (empty($path)) {
            return;
        }

        // Appliquer chaque transition intermédiaire
        $previousStatut = $currentStatut;
        foreach ($path as $step) {
            $record->statut = $step;
            self::enregistrerHistorique($record, $previousStatut, $step, 'Avancement automatique (intra-étape)');
            $previousStatut = $step;
        }

        $record->saveQuietly();

        $etape = $targetStatut->getEtape();
        Notification::make()
            ->title("📍 Statut avancé automatiquement")
            ->body("Le statut est passé de « {$currentStatut->getLabel()} » à « {$targetStatut->getLabel()} » (étape {$etape}/13).")
            ->success()
            ->duration(5000)
            ->send();
    }

    /**
     * Détecte le statut cible en fonction des données remplies dans le formulaire.
     */
    public static function detectTargetStatut($record): ?StatutCandidature
    {
        // Remboursement effectué → Terminé
        if ($record->remboursement_effectue && $record->date_remboursement) {
            return StatutCandidature::TERMINE;
        }

        // Attestation générée
        if ($record->attestation_generee || $record->chemin_attestation) {
            return StatutCandidature::ATTESTATION_GENEREE;
        }

        // Évaluation terminée (note saisie)
        if ($record->note_evaluation !== null && $record->note_evaluation > 0) {
            return StatutCandidature::EVALUATION_TERMINEE;
        }

        // Évaluation en cours (date d'évaluation saisie sans note)
        if ($record->date_evaluation && ($record->note_evaluation === null || $record->note_evaluation == 0)) {
            return StatutCandidature::EN_EVALUATION;
        }

        // Induction terminée
        if ($record->induction_completee) {
            return StatutCandidature::INDUCTION_TERMINEE;
        }

        // Induction planifiée
        if ($record->date_induction && !$record->induction_completee) {
            return StatutCandidature::INDUCTION_PLANIFIEE;
        }

        // Réponse lettre envoyée
        if ($record->reponse_lettre_envoyee) {
            return StatutCandidature::REPONSE_LETTRE_ENVOYEE;
        }

        // Affecté (service + tuteur)
        if ($record->service_affecte && $record->tuteur_id) {
            return StatutCandidature::AFFECTE;
        }

        // Test passé (note de test saisie)
        if ($record->note_test !== null && $record->note_test > 0) {
            return StatutCandidature::TEST_PASSE;
        }

        // Test planifié (date saisie)
        if ($record->date_test && ($record->note_test === null || $record->note_test == 0)) {
            return StatutCandidature::TEST_PLANIFIE;
        }

        return null;
    }

    /**
     * Détecte le statut cible en restant DANS la même étape wizard.
     * Évite de sauter vers une étape wizard ultérieure.
     */
    public static function detectTargetStatutWithinStep($record, int $currentWizardStep): ?StatutCandidature
    {
        $candidates = [
            // Convocation au test (wizard step 5)
            ['condition' => fn ($r) => $r->date_test && ($r->note_test === null || $r->note_test == 0), 'statut' => StatutCandidature::TEST_PLANIFIE],
            // Résultats du test (wizard step 6)
            ['condition' => fn ($r) => $r->note_test !== null && $r->note_test > 0, 'statut' => StatutCandidature::TEST_PASSE],
            // Affectation (wizard step 7)
            ['condition' => fn ($r) => $r->service_affecte && $r->tuteur_id, 'statut' => StatutCandidature::AFFECTE],
            // Induction (wizard step 8)
            ['condition' => fn ($r) => $r->induction_completee, 'statut' => StatutCandidature::INDUCTION_TERMINEE],
            ['condition' => fn ($r) => $r->date_induction && !$r->induction_completee, 'statut' => StatutCandidature::INDUCTION_PLANIFIEE],
            ['condition' => fn ($r) => $r->reponse_lettre_envoyee, 'statut' => StatutCandidature::REPONSE_LETTRE_ENVOYEE],
            // Évaluation (wizard step 9)
            ['condition' => fn ($r) => $r->note_evaluation !== null && $r->note_evaluation > 0, 'statut' => StatutCandidature::EVALUATION_TERMINEE],
            ['condition' => fn ($r) => $r->date_evaluation, 'statut' => StatutCandidature::EN_EVALUATION],
            // Attestation (wizard step 10)
            ['condition' => fn ($r) => $r->attestation_generee || $r->chemin_attestation, 'statut' => StatutCandidature::ATTESTATION_GENEREE],
            // Remboursement (wizard step 11)
            ['condition' => fn ($r) => $r->remboursement_effectue && $r->date_remboursement, 'statut' => StatutCandidature::TERMINE],
        ];

        // Trouver le meilleur statut DANS la même étape wizard
        $bestTarget = null;
        foreach ($candidates as $candidate) {
            $statut = $candidate['statut'];
            $wizardStep = self::getWizardStepForStatut($statut);
            if ($wizardStep !== $currentWizardStep) {
                continue; // Ignorer les statuts d'autres étapes wizard
            }
            if (($candidate['condition'])($record) && $statut->getEtape() > $record->statut->getEtape()) {
                if ($bestTarget === null || $statut->getEtape() > $bestTarget->getEtape()) {
                    $bestTarget = $statut;
                }
            }
        }

        return $bestTarget;
    }

    /**
     * Construit le chemin de transitions valides entre deux statuts.
     * Utilise un BFS (parcours en largeur) pour trouver le chemin le plus court.
     */
    public static function buildTransitionPath(StatutCandidature $from, StatutCandidature $to): array
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
                    // Retourner le chemin sans le premier élément (statut actuel)
                    array_shift($newPath);
                    return $newPath;
                }

                // Limiter la profondeur de recherche
                if (count($newPath) > 15) {
                    continue;
                }

                $visited[$next->value] = true;
                $queue[] = $newPath;
            }
        }

        return []; // Pas de chemin trouvé
    }

    /**
     * Enregistre un changement de statut dans l'historique.
     * Utilise une requête directe pour ne modifier QUE la colonne historique_statuts
     * sans interférer avec les autres attributs dirty du modèle (ex: statut).
     */
    public static function enregistrerHistorique($record, StatutCandidature $de, StatutCandidature $vers, string $commentaire): void
    {
        $historique = $record->historique_statuts ?? [];
        $historique[] = [
            'de' => $de->value,
            'vers' => $vers->value,
            'date' => now()->toIso8601String(),
            'utilisateur' => auth()->user()?->name ?? 'Système',
            'commentaire' => $commentaire,
        ];
        // Mise à jour directe en base pour ne toucher que historique_statuts
        \App\Models\Candidature::withoutEvents(function () use ($record, $historique) {
            \App\Models\Candidature::where('id', $record->id)
                ->update(['historique_statuts' => json_encode($historique)]);
        });
        // Synchroniser l'attribut en mémoire
        $record->historique_statuts = $historique;
    }

    /**
     * Mapping statut → étape du wizard (1-indexé).
     * Les 3 premières étapes (Candidat, Stage souhaité, Documents) sont toujours accessibles.
     * Les étapes 4+ dépendent du statut.
     */
    public static function getWizardStepForStatut(StatutCandidature $statut): int
    {
        return match (true) {
            // Étapes 1-3 du wizard : Candidat, Stage souhaité, Documents → toujours accessibles
            in_array($statut, [
                StatutCandidature::DOSSIER_RECU,
                StatutCandidature::NON_TRAITE,
            ]) => 4, // Ouvrir sur Gestion

            // Étape 4 : Gestion
            in_array($statut, [
                StatutCandidature::ANALYSE_DOSSIER,
                StatutCandidature::DOSSIER_INCOMPLET,
            ]) => 4,

            // Étape 5 : Convocation au test
            in_array($statut, [
                StatutCandidature::ATTENTE_TEST,
                StatutCandidature::TEST_PLANIFIE,
            ]) => 5,

            // Étape 6 : Résultats du test
            in_array($statut, [
                StatutCandidature::TEST_PASSE,
                StatutCandidature::ATTENTE_RESULTATS,
                StatutCandidature::ATTENTE_DECISION,
                StatutCandidature::ACCEPTE,
                StatutCandidature::VALIDE,
            ]) => 6,

            // Étape 7 : Affectation
            in_array($statut, [
                StatutCandidature::PLANIFICATION,
                StatutCandidature::ATTENTE_AFFECTATION,
                StatutCandidature::AFFECTE,
            ]) => 7,

            // Étape 8 : Induction & Réponse
            in_array($statut, [
                StatutCandidature::REPONSE_LETTRE_ENVOYEE,
                StatutCandidature::INDUCTION_PLANIFIEE,
                StatutCandidature::INDUCTION_TERMINEE,
                StatutCandidature::ACCUEIL_SERVICE,
                StatutCandidature::STAGE_EN_COURS,
            ]) => 8,

            // Étape 9 : Évaluation
            in_array($statut, [
                StatutCandidature::EN_EVALUATION,
                StatutCandidature::EVALUATION_TERMINEE,
            ]) => 9,

            // Étape 10 : Attestation
            StatutCandidature::ATTESTATION_GENEREE === $statut => 10,

            // Étape 11 : Remboursement
            in_array($statut, [
                StatutCandidature::REMBOURSEMENT_EN_COURS,
                StatutCandidature::TERMINE,
            ]) => 11,

            // Rejeté → Gestion
            StatutCandidature::REJETE === $statut => 4,

            default => 4,
        };
    }
} 