<?php

namespace App\Filament\Widgets;

use App\Models\Candidature;
use App\Enums\StatutCandidature;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    // Rafraîchir les stats toutes les 30 secondes au lieu de chaque requête
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Cache les stats pendant 60 secondes pour réduire les requêtes DB
        return cache()->remember('filament_stats_overview', 60, function () {
            return $this->computeStats();
        });
    }

    protected function computeStats(): array
    {
        // Requête unique avec agrégation pour remplacer 6 count() séparés
        $stats = Candidature::toBase()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN statut = 'valide' THEN 1 ELSE 0 END) as valides")
            ->selectRaw("SUM(CASE WHEN statut IN ('non_traite','analyse_dossier','attente_test','attente_resultats','attente_affectation') THEN 1 ELSE 0 END) as en_attente")
            ->selectRaw("SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as ce_mois", [now()->month, now()->year])
            ->selectRaw("SUM(CASE WHEN statut = 'valide' AND MONTH(updated_at) = ? AND YEAR(updated_at) = ? THEN 1 ELSE 0 END) as validations_mois", [now()->month, now()->year])
            ->first();

        $totalCandidatures = $stats->total;
        $candidaturesValides = $stats->valides;
        $candidaturesEnAttente = $stats->en_attente;
        $candidaturesCeMois = $stats->ce_mois;
        $validationsCeMois = $stats->validations_mois;
            
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