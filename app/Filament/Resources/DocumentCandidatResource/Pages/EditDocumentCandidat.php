<?php

namespace App\Filament\Resources\DocumentCandidatResource\Pages;

use App\Filament\Resources\DocumentCandidatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentCandidat extends EditRecord
{
    protected static string $resource = DocumentCandidatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
