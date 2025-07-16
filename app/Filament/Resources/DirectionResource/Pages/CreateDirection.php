<?php

namespace App\Filament\Resources\DirectionResource\Pages;

use App\Filament\Resources\DirectionResource;
use App\Models\ConfigurationListe;
use Filament\Resources\Pages\CreateRecord;

class CreateDirection extends CreateRecord
{
    protected static string $resource = DirectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type_liste'] = ConfigurationListe::TYPE_DIRECTION;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 