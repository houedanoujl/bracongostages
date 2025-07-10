<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidature_id',
        'note_plateforme',
        'note_processus',
        'commentaires',
        'recommandation',
        'suggestions_amelioration',
    ];

    protected $casts = [
        'note_plateforme' => 'integer',
        'note_processus' => 'integer',
    ];

    /**
     * Relation avec la candidature
     */
    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }

    /**
     * Accessor pour la note moyenne
     */
    public function getNoteMoyenneAttribute(): float
    {
        if ($this->note_plateforme && $this->note_processus) {
            return ($this->note_plateforme + $this->note_processus) / 2;
        }
        
        return $this->note_plateforme ?: $this->note_processus ?: 0;
    }

    /**
     * Accessor pour le niveau de satisfaction
     */
    public function getNiveauSatisfactionAttribute(): string
    {
        $moyenne = $this->note_moyenne;
        
        return match (true) {
            $moyenne >= 4.5 => 'Très satisfait',
            $moyenne >= 3.5 => 'Satisfait',
            $moyenne >= 2.5 => 'Neutre',
            $moyenne >= 1.5 => 'Insatisfait',
            default => 'Très insatisfait',
        };
    }

    /**
     * Accessor pour la couleur du niveau de satisfaction
     */
    public function getCouleurSatisfactionAttribute(): string
    {
        $moyenne = $this->note_moyenne;
        
        return match (true) {
            $moyenne >= 4.5 => 'green',
            $moyenne >= 3.5 => 'blue',
            $moyenne >= 2.5 => 'yellow',
            $moyenne >= 1.5 => 'orange',
            default => 'red',
        };
    }

    /**
     * Scope pour les évaluations récentes
     */
    public function scopeRecentes($query, int $jours = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($jours));
    }

    /**
     * Scope pour filtrer par note minimale
     */
    public function scopeNoteMinimale($query, float $note)
    {
        return $query->where(function ($q) use ($note) {
            $q->where('note_plateforme', '>=', $note)
              ->orWhere('note_processus', '>=', $note);
        });
    }

    /**
     * Scope pour les évaluations positives (>= 3.5)
     */
    public function scopePositives($query)
    {
        return $query->where(function ($q) {
            $q->where('note_plateforme', '>=', 3.5)
              ->orWhere('note_processus', '>=', 3.5);
        });
    }

    /**
     * Scope pour les évaluations négatives (< 2.5)
     */
    public function scopeNegatives($query)
    {
        return $query->where(function ($q) {
            $q->where('note_plateforme', '<', 2.5)
              ->orWhere('note_processus', '<', 2.5);
        });
    }

    /**
     * Obtenir les statistiques d'évaluation
     */
    public static function getStatistiques(): array
    {
        $evaluations = self::all();
        
        if ($evaluations->isEmpty()) {
            return [
                'total' => 0,
                'note_plateforme_moyenne' => 0,
                'note_processus_moyenne' => 0,
                'note_generale_moyenne' => 0,
                'satisfaction_positive' => 0,
                'taux_satisfaction' => 0,
            ];
        }

        $notePlateforme = $evaluations->avg('note_plateforme') ?: 0;
        $noteProcessus = $evaluations->avg('note_processus') ?: 0;
        $noteGenerale = ($notePlateforme + $noteProcessus) / 2;
        
        $satisfactionPositive = $evaluations->filter(function ($evaluation) {
            return $evaluation->note_moyenne >= 3.5;
        })->count();

        return [
            'total' => $evaluations->count(),
            'note_plateforme_moyenne' => round($notePlateforme, 2),
            'note_processus_moyenne' => round($noteProcessus, 2),
            'note_generale_moyenne' => round($noteGenerale, 2),
            'satisfaction_positive' => $satisfactionPositive,
            'taux_satisfaction' => $evaluations->count() > 0 
                ? round(($satisfactionPositive / $evaluations->count()) * 100, 1) 
                : 0,
        ];
    }

    /**
     * Validation des règles
     */
    public static function rules(): array
    {
        return [
            'note_plateforme' => 'nullable|integer|min:1|max:5',
            'note_processus' => 'nullable|integer|min:1|max:5',
            'commentaires' => 'nullable|string|max:1000',
            'recommandation' => 'nullable|boolean',
            'suggestions_amelioration' => 'nullable|string|max:1000',
        ];
    }
} 