<?php

namespace App\Filament\Resources\DirectionResource\Pages;

use App\Filament\Resources\DirectionResource;
use App\Models\ConfigurationListe;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListDirections extends ListRecords
{
    protected static string $resource = DirectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['type_liste'] = ConfigurationListe::TYPE_DIRECTION;
                    return $data;
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous')
                ->badge(ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_DIRECTION)->count()),
            'active' => Tab::make('Actifs')
                ->modifyQueryUsing(fn ($query) => $query->where('actif', true))
                ->badge(ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_DIRECTION)->where('actif', true)->count()),
            'inactive' => Tab::make('Inactifs')
                ->modifyQueryUsing(fn ($query) => $query->where('actif', false))
                ->badge(ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_DIRECTION)->where('actif', false)->count()),
        ];
    }
} 