<?php

namespace App\Filament\Resources\EtablissementPartenaireResource\Pages;

use App\Filament\Resources\EtablissementPartenaireResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEtablissementPartenaire extends EditRecord
{
    protected static string $resource = EtablissementPartenaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
