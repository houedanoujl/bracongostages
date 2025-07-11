<?php

namespace App\Filament\Resources\StatistiqueAccueilResource\Pages;

use App\Filament\Resources\StatistiqueAccueilResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatistiqueAccueils extends ListRecords
{
    protected static string $resource = StatistiqueAccueilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
