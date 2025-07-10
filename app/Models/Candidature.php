<?php

namespace App\Models;

use App\Enums\StatutCandidature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Candidature extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom', 
        'telephone',
        'email',
        'etablissement',
        'niveau_etude',
        'faculte',
        'objectif_stage',
        'directions_souhaitees',
        'projets_souhaites',
        'competences_souhaitees',
        'periode_debut_souhaitee',
        'periode_fin_souhaitee',
        'statut',
        'motif_rejet',
        'date_debut_stage',
        'date_fin_stage',
        'code_suivi',
    ];

    protected $casts = [
        'directions_souhaitees' => 'array',
        'periode_debut_souhaitee' => 'date',
        'periode_fin_souhaitee' => 'date',
        'date_debut_stage' => 'date',
        'date_fin_stage' => 'date',
        'statut' => StatutCandidature::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($candidature) {
            if (empty($candidature->code_suivi)) {
                $candidature->code_suivi = 'BRC-' . strtoupper(Str::random(8));
            }
            if (empty($candidature->statut)) {
                $candidature->statut = StatutCandidature::NON_TRAITE;
            }
        });
    }

    /**
     * Relation avec les documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Relation avec l'évaluation
     */
    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class);
    }

    /**
     * Accessor pour le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Accessor pour la durée du stage souhaitée
     */
    public function getDureeSouhaiteeAttribute(): ?int
    {
        if ($this->periode_debut_souhaitee && $this->periode_fin_souhaitee) {
            return $this->periode_debut_souhaitee->diffInDays($this->periode_fin_souhaitee);
        }
        return null;
    }

    /**
     * Accessor pour vérifier si la candidature est terminée
     */
    public function getEstTermineeAttribute(): bool
    {
        return $this->statut->isTerminal();
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeParStatut($query, StatutCandidature $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour filtrer par établissement
     */
    public function scopeParEtablissement($query, string $etablissement)
    {
        return $query->where('etablissement', $etablissement);
    }

    /**
     * Scope pour filtrer par niveau d'étude
     */
    public function scopeParNiveau($query, string $niveau)
    {
        return $query->where('niveau_etude', $niveau);
    }

    /**
     * Scope pour les candidatures récentes (dernière semaine)
     */
    public function scopeRecentes($query)
    {
        return $query->where('created_at', '>=', now()->subWeek());
    }

    /**
     * Changer le statut de la candidature
     */
    public function changerStatut(StatutCandidature $nouveauStatut, ?string $motifRejet = null): bool
    {
        if (!$this->statut->canTransitionTo($nouveauStatut)) {
            return false;
        }

        $ancienStatut = $this->statut;
        $this->statut = $nouveauStatut;
        
        if ($nouveauStatut === StatutCandidature::REJETE && $motifRejet) {
            $this->motif_rejet = $motifRejet;
        }

        $saved = $this->save();

        if ($saved && $ancienStatut !== $nouveauStatut) {
            // Envoyer une notification asynchrone
            \App\Jobs\SendCandidatureNotification::dispatch($this, $ancienStatut, $nouveauStatut);
        }

        return $saved;
    }

    /**
     * Valider la candidature avec dates de stage
     */
    public function valider(\Carbon\Carbon $dateDebut, \Carbon\Carbon $dateFin): bool
    {
        $ancienStatut = $this->statut;
        $this->statut = StatutCandidature::VALIDE;
        $this->date_debut_stage = $dateDebut;
        $this->date_fin_stage = $dateFin;
        $this->motif_rejet = null;

        $saved = $this->save();

        if ($saved && $ancienStatut !== StatutCandidature::VALIDE) {
            // Envoyer une notification asynchrone
            \App\Jobs\SendCandidatureNotification::dispatch($this, $ancienStatut, StatutCandidature::VALIDE);
        }

        return $saved;
    }

    /**
     * Rejeter la candidature
     */
    public function rejeter(string $motif): bool
    {
        $ancienStatut = $this->statut;
        $this->statut = StatutCandidature::REJETE;
        $this->motif_rejet = $motif;
        $this->date_debut_stage = null;
        $this->date_fin_stage = null;

        $saved = $this->save();

        if ($saved && $ancienStatut !== StatutCandidature::REJETE) {
            // Envoyer une notification asynchrone
            \App\Jobs\SendCandidatureNotification::dispatch($this, $ancienStatut, StatutCandidature::REJETE);
        }

        return $saved;
    }

    /**
     * Obtenir les directions disponibles
     */
    public static function getDirectionsDisponibles(): array
    {
        return [
            'Direction Générale',
            'Direction Financière et Comptable',
            'Direction des Ressources Humaines',
            'Direction Marketing et Communication',
            'Direction Commerciale',
            'Direction de Production',
            'Direction Technique',
            'Direction Qualité',
            'Direction Logistique',
            'Direction Informatique',
            'Direction Juridique',
            'Direction Audit Interne',
        ];
    }

    /**
     * Obtenir les établissements référencés
     */
    public static function getEtablissements(): array
    {
        return [
            'Université de Kinshasa (UNIKIN)',
            'Université Libre de Kinshasa (ULK)',
            'Université Protestante du Congo (UPC)',
            'Institut Supérieur de Commerce (ISC)',
            'Institut Supérieur de Techniques Appliquées (ISTA)',
            'École Supérieure de Gestion (ESG)',
            'Institut Supérieur Pédagogique (ISP)',
            'Université Pédagogique Nationale (UPN)',
            'Institut Facultaire des Sciences de l\'Information et de la Communication (IFASIC)',
            'École Supérieure des Ingénieurs Industriels (ESII)',
            'Autres',
        ];
    }

    /**
     * Obtenir les niveaux d'étude
     */
    public static function getNiveauxEtude(): array
    {
        return [
            'Bac+1' => 'Première année (Bac+1)',
            'Bac+2' => 'Deuxième année (Bac+2)', 
            'Bac+3' => 'Licence/Graduat (Bac+3)',
            'Bac+4' => 'Maîtrise (Bac+4)',
            'Bac+5' => 'Master (Bac+5)',
            'Doctorat' => 'Doctorat/PhD',
            'École Secondaire' => 'École Secondaire',
        ];
    }
} 