<?php

namespace App\Filament\Resources\TémoignageResource\Pages;

use App\Filament\Resources\TémoignageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTémoignage extends EditRecord
{
    protected static string $resource = TémoignageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
