<?php

namespace App\Filament\Widgets;

use App\Models\Evaluation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EvaluationStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $evaluations = Evaluation::all();
        
        if ($evaluations->isEmpty()) {
            return [
                Stat::make('Évaluations reçues', '0')
                    ->description('Aucune évaluation pour le moment')
                    ->descriptionIcon('heroicon-m-chart-bar')
                    ->color('gray'),
                Stat::make('Note moyenne', 'N/A')
                    ->description('Pas encore d\'évaluations')
                    ->descriptionIcon('heroicon-m-star')
                    ->color('gray'),
                Stat::make('Taux de satisfaction', '0%')
                    ->description('Évaluations ≥ 4/5')
                    ->descriptionIcon('heroicon-m-heart')
                    ->color('gray'),
            ];
        }

        $noteMoyenne = $evaluations->avg('note_moyenne');
        $satisfactionPositive = $evaluations->where('note_moyenne', '>=', 4.0)->count();
        $tauxSatisfaction = ($satisfactionPositive / $evaluations->count()) * 100;
        
        $recommandationsPositives = $evaluations->where('recommandation', 'oui')->count();
        $tauxRecommandation = ($recommandationsPositives / $evaluations->count()) * 100;

        return [
            Stat::make('Évaluations reçues', $evaluations->count())
                ->description('Total des retours stagiaires')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('blue'),
            
            Stat::make('Note moyenne', number_format($noteMoyenne, 1) . '/5')
                ->description('Satisfaction globale')
                ->descriptionIcon('heroicon-m-star')
                ->color($noteMoyenne >= 4.0 ? 'success' : ($noteMoyenne >= 3.0 ? 'warning' : 'danger')),
            
            Stat::make('Taux de satisfaction', round($tauxSatisfaction, 1) . '%')
                ->description('Évaluations ≥ 4/5')
                ->descriptionIcon('heroicon-m-heart')
                ->color($tauxSatisfaction >= 80 ? 'success' : ($tauxSatisfaction >= 60 ? 'warning' : 'danger')),
            
            Stat::make('Recommandations', round($tauxRecommandation, 1) . '%')
                ->description('Recommanderait BRACONGO')
                ->descriptionIcon('heroicon-m-hand-thumb-up')
                ->color($tauxRecommandation >= 80 ? 'success' : ($tauxRecommandation >= 60 ? 'warning' : 'danger')),
        ];
    }
} 