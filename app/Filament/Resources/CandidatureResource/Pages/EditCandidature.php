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
        // Valider la transition de statut côté serveur
        if (isset($data['statut'])) {
            $currentStatut = $this->record->statut;
            $newStatut = StatutCandidature::tryFrom($data['statut']);

            if ($currentStatut && $newStatut && $currentStatut !== $newStatut) {
                if (!$currentStatut->canTransitionTo($newStatut)) {
                    Notification::make()
                        ->title('Transition de statut non autorisée')
                        ->body("Impossible de passer de \"{$currentStatut->getLabel()}\" à \"{$newStatut->getLabel()}\".")
                        ->danger()
                        ->send();

                    $this->halt();
                }
            }
        }

        return $data;
    }
} 