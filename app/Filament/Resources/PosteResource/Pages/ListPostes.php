<?php

namespace App\Filament\Resources\PosteResource\Pages;

use App\Filament\Resources\PosteResource;
use App\Models\ConfigurationListe;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListPostes extends ListRecords
{
    protected static string $resource = PosteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['type_liste'] = ConfigurationListe::TYPE_POSTE;
                    return $data;
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous')
                ->badge(ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_POSTE)->count()),
            'active' => Tab::make('Actifs')
                ->modifyQueryUsing(fn ($query) => $query->where('actif', true))
                ->badge(ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_POSTE)->where('actif', true)->count()),
            'inactive' => Tab::make('Inactifs')
                ->modifyQueryUsing(fn ($query) => $query->where('actif', false))
                ->badge(ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_POSTE)->where('actif', false)->count()),
        ];
    }
} 