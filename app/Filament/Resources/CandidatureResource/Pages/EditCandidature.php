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
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Valider la transition de statut côté serveur (empêcher le saut d'étapes)
        if (isset($data['statut'])) {
            $currentStatut = $this->record->statut;
            $newStatut = StatutCandidature::tryFrom($data['statut']);

            if ($currentStatut && $newStatut && $currentStatut !== $newStatut) {
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
     * Après la sauvegarde, si le statut a changé, on passe par changerStatut()
     * pour déclencher l'historique et les emails automatiques
     */
    protected function afterSave(): void
    {
        $record = $this->record;
        $originalStatut = $record->getOriginal('statut');

        // Si le statut a changé via le formulaire, reconstruire l'historique
        if ($originalStatut && $record->statut->value !== $originalStatut) {
            $ancienStatut = StatutCandidature::tryFrom($originalStatut);
            if ($ancienStatut) {
                // Ajouter à l'historique manuellement (la transition a déjà été faite par Eloquent save)
                $historique = $record->historique_statuts ?? [];
                $historique[] = [
                    'de' => $ancienStatut->value,
                    'vers' => $record->statut->value,
                    'date' => now()->toIso8601String(),
                    'utilisateur' => auth()->user()?->name ?? 'Système',
                    'commentaire' => 'Modifié via le formulaire d\'édition',
                ];
                $record->updateQuietly(['historique_statuts' => $historique]);
            }
        }
    }
} 