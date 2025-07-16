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
        'satisfaction_generale',
        'recommandation',
        'accueil_integration',
        'encadrement_suivi',
        'conditions_travail',
        'ambiance_travail',
        'competences_developpees',
        'reponse_attentes',
        'aspects_enrichissants',
        'suggestions_amelioration',
        'contact_futur',
        'commentaire_libre',
        'note_moyenne',
    ];

    protected $casts = [
        'satisfaction_generale' => 'integer',
        'note_moyenne' => 'decimal:1',
    ];

    /**
     * Relation avec la candidature
     */
    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }

    /**
     * Calculer la note moyenne automatiquement
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($evaluation) {
            $notes = [];
            
            if ($evaluation->satisfaction_generale) {
                $notes[] = $evaluation->satisfaction_generale;
            }
            
            if ($evaluation->accueil_integration) {
                $notes[] = $evaluation->convertirNote($evaluation->accueil_integration);
            }
            
            if ($evaluation->encadrement_suivi) {
                $notes[] = $evaluation->convertirNote($evaluation->encadrement_suivi);
            }
            
            if ($evaluation->conditions_travail) {
                $notes[] = $evaluation->convertirNote($evaluation->conditions_travail);
            }
            
            if ($evaluation->ambiance_travail) {
                $notes[] = $evaluation->convertirNote($evaluation->ambiance_travail);
            }
            
            if (!empty($notes)) {
                $evaluation->note_moyenne = round(array_sum($notes) / count($notes), 1);
            }
        });
    }

    /**
     * Convertir les notes textuelles en valeurs numériques
     */
    private function convertirNote(string $note): int
    {
        return match ($note) {
            'excellent' => 5,
            'bon' => 4,
            'moyen' => 3,
            'insuffisant' => 2,
            default => 0,
        };
    }

    /**
     * Obtenir le label de satisfaction générale
     */
    public function getSatisfactionGeneraleLabelAttribute(): string
    {
        return match ($this->satisfaction_generale) {
            1 => 'Très décevant',
            2 => 'Décevant',
            3 => 'Moyen',
            4 => 'Satisfaisant',
            5 => 'Excellent',
            default => 'Non évalué',
        };
    }

    /**
     * Obtenir le label de recommandation
     */
    public function getRecommandationLabelAttribute(): string
    {
        return match ($this->recommandation) {
            'oui' => 'Oui, absolument',
            'peut_etre' => 'Peut-être',
            'non' => 'Non',
            default => 'Non spécifié',
        };
    }

    /**
     * Obtenir le label de contact futur
     */
    public function getContactFuturLabelAttribute(): string
    {
        return match ($this->contact_futur) {
            'oui' => 'Oui, pour des opportunités futures',
            'non' => 'Non',
            default => 'Non spécifié',
        };
    }

    /**
     * Vérifier si l'évaluation est positive (note moyenne >= 4)
     */
    public function getEstPositiveAttribute(): bool
    {
        return $this->note_moyenne >= 4.0;
    }

    /**
     * Obtenir la couleur de la note moyenne
     */
    public function getNoteCouleurAttribute(): string
    {
        if ($this->note_moyenne >= 4.5) return 'green';
        if ($this->note_moyenne >= 3.5) return 'yellow';
        if ($this->note_moyenne >= 2.5) return 'orange';
        return 'red';
    }

    /**
     * Scope pour les évaluations positives
     */
    public function scopePositives($query)
    {
        return $query->where('note_moyenne', '>=', 4.0);
    }

    /**
     * Scope pour les évaluations récentes
     */
    public function scopeRecentes($query, int $jours = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($jours));
    }

    /**
     * Scope pour filtrer par niveau de satisfaction
     */
    public function scopeParSatisfaction($query, int $niveau)
    {
        return $query->where('satisfaction_generale', $niveau);
    }
} 