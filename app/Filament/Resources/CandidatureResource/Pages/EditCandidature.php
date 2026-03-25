<?php

namespace App\Filament\Resources\CandidatureResource\Pages;

use App\Filament\Resources\CandidatureResource;
use App\Enums\StatutCandidature;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCandidature extends EditRecord
{
    protected static string $resource = CandidatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save_header')
                ->label('Sauvegarder')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->save();
                })
                ->keyBindings(['mod+s']),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Sauvegarder toutes les modifications')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            $this->getCancelFormAction(),
        ];
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
     * Avance automatiquement le statut si les données saisies correspondent à une étape supérieure.
     * Par exemple : si on note un test alors que le statut est "analyse_dossier", on fait avancer
     * le statut automatiquement en respectant le workflow.
     */
    public static function autoAdvanceStatut($record): void
    {
        $currentStatut = $record->statut;
        $targetStatut = self::detectTargetStatut($record);

        if (!$targetStatut || $targetStatut === $currentStatut) {
            return;
        }

        // Vérifier que le target est "plus avancé" que le statut actuel
        if ($targetStatut->getEtape() <= $currentStatut->getEtape()) {
            return;
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
            self::enregistrerHistorique($record, $previousStatut, $step, 'Avancement automatique basé sur les données saisies');
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

            // Étape 5 : Tests
            in_array($statut, [
                StatutCandidature::ATTENTE_TEST,
                StatutCandidature::TEST_PLANIFIE,
                StatutCandidature::TEST_PASSE,
                StatutCandidature::ATTENTE_RESULTATS,
                StatutCandidature::ATTENTE_DECISION,
                StatutCandidature::ACCEPTE,
                StatutCandidature::VALIDE,
            ]) => 5,

            // Étape 6 : Affectation
            in_array($statut, [
                StatutCandidature::PLANIFICATION,
                StatutCandidature::ATTENTE_AFFECTATION,
                StatutCandidature::AFFECTE,
            ]) => 6,

            // Étape 7 : Induction & Réponse
            in_array($statut, [
                StatutCandidature::REPONSE_LETTRE_ENVOYEE,
                StatutCandidature::INDUCTION_PLANIFIEE,
                StatutCandidature::INDUCTION_TERMINEE,
                StatutCandidature::ACCUEIL_SERVICE,
                StatutCandidature::STAGE_EN_COURS,
            ]) => 7,

            // Étape 8 : Évaluation
            in_array($statut, [
                StatutCandidature::EN_EVALUATION,
                StatutCandidature::EVALUATION_TERMINEE,
            ]) => 8,

            // Étape 9 : Attestation
            StatutCandidature::ATTESTATION_GENEREE === $statut => 9,

            // Étape 10 : Remboursement
            in_array($statut, [
                StatutCandidature::REMBOURSEMENT_EN_COURS,
                StatutCandidature::TERMINE,
            ]) => 10,

            // Rejeté → Gestion
            StatutCandidature::REJETE === $statut => 4,

            default => 4,
        };
    }
} 