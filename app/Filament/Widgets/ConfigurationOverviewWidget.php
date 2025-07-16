<?php

namespace App\Filament\Widgets;

use App\Models\ConfigurationListe;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ConfigurationOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Établissements', ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_ETABLISSEMENT)->count())
                ->description('Total des établissements configurés')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary')
                ->url(route('filament.admin.resources.etablissements.index')),

            Stat::make('Niveaux d\'étude', ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_NIVEAU_ETUDE)->count())
                ->description('Total des niveaux d\'étude')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->url(route('filament.admin.resources.niveau-etudes.index')),

            Stat::make('Directions', ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_DIRECTION)->count())
                ->description('Total des directions')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('warning')
                ->url(route('filament.admin.resources.directions.index')),

            Stat::make('Postes disponibles', ConfigurationListe::where('type_liste', ConfigurationListe::TYPE_POSTE)->count())
                ->description('Total des postes configurés')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info')
                ->url(route('filament.admin.resources.postes.index')),
        ];
    }
} 