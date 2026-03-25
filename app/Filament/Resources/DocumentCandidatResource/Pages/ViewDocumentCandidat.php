<?php

namespace App\Filament\Resources\DocumentCandidatResource\Pages;

use App\Filament\Resources\DocumentCandidatResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDocumentCandidat extends ViewRecord
{
    protected static string $resource = DocumentCandidatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
