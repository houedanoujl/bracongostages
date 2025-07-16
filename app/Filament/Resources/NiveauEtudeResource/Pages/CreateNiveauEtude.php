<?php

namespace App\Filament\Resources\NiveauEtudeResource\Pages;

use App\Filament\Resources\NiveauEtudeResource;
use App\Models\ConfigurationListe;
use Filament\Resources\Pages\CreateRecord;

class CreateNiveauEtude extends CreateRecord
{
    protected static string $resource = NiveauEtudeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type_liste'] = ConfigurationListe::TYPE_NIVEAU_ETUDE;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 