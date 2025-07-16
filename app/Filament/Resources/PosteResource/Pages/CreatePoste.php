<?php

namespace App\Filament\Resources\PosteResource\Pages;

use App\Filament\Resources\PosteResource;
use App\Models\ConfigurationListe;
use Filament\Resources\Pages\CreateRecord;

class CreatePoste extends CreateRecord
{
    protected static string $resource = PosteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type_liste'] = ConfigurationListe::TYPE_POSTE;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 