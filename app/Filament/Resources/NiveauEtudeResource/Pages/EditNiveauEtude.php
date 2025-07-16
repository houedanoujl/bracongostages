<?php

namespace App\Filament\Resources\NiveauEtudeResource\Pages;

use App\Filament\Resources\NiveauEtudeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNiveauEtude extends EditRecord
{
    protected static string $resource = NiveauEtudeResource::class;

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