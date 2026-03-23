<?php

namespace App\Filament\Widgets;

use App\Models\Evaluation;
use App\Models\Candidature;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EvaluationStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $evaluations = Evaluation::all();
        
        // Nombre de stagiaires évalués (via note_evaluation dans candidatures)
        $stagiairesEvalues = Candidature::whereNotNull('note_evaluation')->count();
        
        // Note moyenne des stagiaires (note_evaluation sur 20)
        $noteMoyenneStagiaires = Candidature::whereNotNull('note_evaluation')->avg('note_evaluation');
        
        if ($evaluations->isEmpty() && $stagiairesEvalues === 0) {
            return [
                Stat::make('Stagiaires évalués', '0')
                    ->description('Aucune évaluation pour le moment')
                    ->descriptionIcon('heroicon-m-academic-cap')
                    ->color('gray'),
                Stat::make('Note moyenne stagiaires', 'N/A')
                    ->description('Pas encore d\'évaluations')
                    ->descriptionIcon('heroicon-m-star')
                    ->color('gray'),
                Stat::make('Retours d\'expérience', '0')
                    ->description('Aucun retour stagiaire')
                    ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                    ->color('gray'),
            ];
        }

        $stats = [];
        
        // Stat 1: Nombre de stagiaires évalués
        $stats[] = Stat::make('Stagiaires évalués', $stagiairesEvalues)
            ->description('Évalués par les tuteurs')
            ->descriptionIcon('heroicon-m-academic-cap')
            ->color('blue');
        
        // Stat 2: Note moyenne des stagiaires (sur 20)
        $stats[] = Stat::make('Note moyenne stagiaires', $noteMoyenneStagiaires ? number_format($noteMoyenneStagiaires, 1) . '/20' : 'N/A')
            ->description('Moyenne des notes d\'évaluation')
            ->descriptionIcon('heroicon-m-star')
            ->color($noteMoyenneStagiaires >= 16 ? 'success' : ($noteMoyenneStagiaires >= 12 ? 'warning' : 'danger'));
        
        // Stat 3-7: Nombre de stagiaires par mention avec poids
        // Mentions basées sur note_evaluation /20
        $mentions = [
            'Excellent' => ['min' => 16, 'max' => 20, 'color' => 'success', 'icon' => 'heroicon-m-trophy'],
            'Très bon' => ['min' => 14, 'max' => 15.99, 'color' => 'success', 'icon' => 'heroicon-m-hand-thumb-up'],
            'Bon' => ['min' => 12, 'max' => 13.99, 'color' => 'info', 'icon' => 'heroicon-m-check-circle'],
            'Moyen' => ['min' => 10, 'max' => 11.99, 'color' => 'warning', 'icon' => 'heroicon-m-minus-circle'],
            'Mauvais' => ['min' => 0, 'max' => 9.99, 'color' => 'danger', 'icon' => 'heroicon-m-exclamation-triangle'],
        ];
        
        foreach ($mentions as $mention => $config) {
            $count = Candidature::whereNotNull('note_evaluation')
                ->where('note_evaluation', '>=', $config['min'])
                ->where('note_evaluation', '<=', $config['max'])
                ->count();
            
            $poids = $stagiairesEvalues > 0 ? round(($count / $stagiairesEvalues) * 100, 1) : 0;
            
            $stats[] = Stat::make($mention, $count)
                ->description($poids . '% des évalués')
                ->descriptionIcon($config['icon'])
                ->color($config['color']);
        }
        
        // Stats retours d'expérience (formulaire stagiaire)
        if ($evaluations->isNotEmpty()) {
            $noteMoyenneRetour = $evaluations->avg('note_moyenne');
            $satisfactionPositive = $evaluations->where('note_moyenne', '>=', 4.0)->count();
            $tauxSatisfaction = ($satisfactionPositive / $evaluations->count()) * 100;
            
            $recommandationsPositives = $evaluations->where('recommandation', 'oui')->count();
            $tauxRecommandation = ($recommandationsPositives / $evaluations->count()) * 100;

            $stats[] = Stat::make('Retours d\'expérience', $evaluations->count())
                ->description('Formulaires remplis par les stagiaires')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('blue');
            
            $stats[] = Stat::make('Satisfaction moyenne', number_format($noteMoyenneRetour, 1) . '/5')
                ->description(round($tauxSatisfaction, 1) . '% satisfaits (≥ 4/5)')
                ->descriptionIcon('heroicon-m-heart')
                ->color($tauxSatisfaction >= 80 ? 'success' : ($tauxSatisfaction >= 60 ? 'warning' : 'danger'));
            
            $stats[] = Stat::make('Recommandations', round($tauxRecommandation, 1) . '%')
                ->description('Recommanderait BRACONGO')
                ->descriptionIcon('heroicon-m-hand-thumb-up')
                ->color($tauxRecommandation >= 80 ? 'success' : ($tauxRecommandation >= 60 ? 'warning' : 'danger'));
        }

        return $stats;
    }

    protected function getColumns(): int
    {
        return 3;
    }
} 