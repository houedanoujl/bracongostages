<?php

namespace App\Filament\Resources\EtablissementResource\Pages;

use App\Filament\Resources\EtablissementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEtablissement extends EditRecord
{
    protected static string $resource = EtablissementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 