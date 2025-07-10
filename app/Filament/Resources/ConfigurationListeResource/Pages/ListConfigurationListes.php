<?php

namespace App\Filament\Resources\ConfigurationListeResource\Pages;

use App\Filament\Resources\ConfigurationListeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConfigurationListes extends ListRecords
{
    protected static string $resource = ConfigurationListeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
