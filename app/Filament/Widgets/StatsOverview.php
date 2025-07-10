<?php

namespace App\Filament\Widgets;

use App\Models\Candidature;
use App\Enums\StatutCandidature;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCandidatures = Candidature::count();
        $candidaturesValides = Candidature::where('statut', StatutCandidature::VALIDE)->count();
        $candidaturesEnAttente = Candidature::whereIn('statut', [
            StatutCandidature::NON_TRAITE,
            StatutCandidature::ANALYSE_DOSSIER,
            StatutCandidature::ATTENTE_TEST,
            StatutCandidature::ATTENTE_RESULTATS,
            StatutCandidature::ATTENTE_AFFECTATION
        ])->count();
        
        $candidaturesCeMois = Candidature::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        $tauxValidation = $totalCandidatures > 0 ? 
            round(($candidaturesValides / $totalCandidatures) * 100, 1) : 0;

        // Top 3 des établissements
        $topEtablissements = Candidature::select('etablissement', DB::raw('count(*) as total'))
            ->groupBy('etablissement')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get()
            ->pluck('etablissement')
            ->join(', ');

        // Distribution par statut pour ce mois
        $nouveaucesMois = Candidature::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        $validationsCeMois = Candidature::where('statut', StatutCandidature::VALIDE)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        return [
            Stat::make('Total des candidatures', $totalCandidatures)
                ->description('Toutes les candidatures')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Candidatures validées', $candidaturesValides)
                ->description($tauxValidation . '% de taux de validation')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('En cours de traitement', $candidaturesEnAttente)
                ->description('Candidatures en attente')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Ce mois-ci', $candidaturesCeMois)
                ->description('Nouvelles candidatures')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Validations du mois', $validationsCeMois)
                ->description('Stages confirmés ce mois')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Top établissements', '')
                ->description($topEtablissements ?: 'Aucune donnée')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('gray'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
} 