<?php

namespace App\Filament\Resources\EtablissementResource\Pages;

use App\Filament\Resources\EtablissementResource;
use App\Models\ConfigurationListe;
use Filament\Resources\Pages\CreateRecord;

class CreateEtablissement extends CreateRecord
{
    protected static string $resource = EtablissementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type_liste'] = ConfigurationListe::TYPE_ETABLISSEMENT;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 