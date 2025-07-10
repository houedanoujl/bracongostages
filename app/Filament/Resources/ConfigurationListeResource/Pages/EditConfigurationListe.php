<?php

namespace App\Filament\Resources\ConfigurationListeResource\Pages;

use App\Filament\Resources\ConfigurationListeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConfigurationListe extends EditRecord
{
    protected static string $resource = ConfigurationListeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
