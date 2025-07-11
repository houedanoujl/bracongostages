<?php

namespace App\Filament\Resources\StatistiqueAccueilResource\Pages;

use App\Filament\Resources\StatistiqueAccueilResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatistiqueAccueil extends EditRecord
{
    protected static string $resource = StatistiqueAccueilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
