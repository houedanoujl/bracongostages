<?php

namespace App\Filament\Resources\DocumentCandidatResource\Pages;

use App\Filament\Resources\DocumentCandidatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentsCandidat extends ListRecords
{
    protected static string $resource = DocumentCandidatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un document'),
        ];
    }
}
