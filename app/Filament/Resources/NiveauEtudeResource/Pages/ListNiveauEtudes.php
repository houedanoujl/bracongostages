<?php

namespace App\Filament\Resources\NiveauEtudeResource\Pages;

use App\Filament\Resources\NiveauEtudeResource;
use App\Models\ConfigurationListe;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListNiveauEtudes extends ListRecords
{
    protected static string $resource = NiveauEtudeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['type_liste'] = ConfigurationListe::TYPE_NIVEAU_ETUDE;
                    return $data;
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous')
                ->badge(ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_NIVEAU_ETUDE)->count()),
            'active' => Tab::make('Actifs')
                ->modifyQueryUsing(fn ($query) => $query->where('actif', true))
                ->badge(ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_NIVEAU_ETUDE)->where('actif', true)->count()),
            'inactive' => Tab::make('Inactifs')
                ->modifyQueryUsing(fn ($query) => $query->where('actif', false))
                ->badge(ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_NIVEAU_ETUDE)->where('actif', false)->count()),
        ];
    }
} 