<?php

namespace App\Filament\Resources\DocumentCandidatResource\Pages;

use App\Filament\Resources\DocumentCandidatResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentCandidat extends CreateRecord
{
    protected static string $resource = DocumentCandidatResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
