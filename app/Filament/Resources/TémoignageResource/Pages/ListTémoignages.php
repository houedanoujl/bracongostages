<?php

namespace App\Filament\Resources\TémoignageResource\Pages;

use App\Filament\Resources\TémoignageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTémoignages extends ListRecords
{
    protected static string $resource = TémoignageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
