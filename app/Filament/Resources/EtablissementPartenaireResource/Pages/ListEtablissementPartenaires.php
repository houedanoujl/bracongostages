<?php

namespace App\Filament\Resources\EtablissementPartenaireResource\Pages;

use App\Filament\Resources\EtablissementPartenaireResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEtablissementPartenaires extends ListRecords
{
    protected static string $resource = EtablissementPartenaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
