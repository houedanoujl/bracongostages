<?php

namespace App\Models;

use App\Enums\StatutCandidature;
use App\Models\ConfigurationListe;
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
        'etablissement_autre',
        'niveau_etude',
        'faculte',
        'objectif_stage',
        'poste_souhaite',
        'opportunite_id',
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
            
            // Envoyer une notification email si le candidat a un compte
            $candidat = \App\Models\Candidat::where('email', $this->email)->first();
            if ($candidat) {
                $candidat->notify(new \App\Notifications\CandidatureStatusChanged($this, $ancienStatut, $nouveauStatut));
            }
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
            
            // Envoyer une notification email si le candidat a un compte
            $candidat = \App\Models\Candidat::where('email', $this->email)->first();
            if ($candidat) {
                $candidat->notify(new \App\Notifications\CandidatureStatusChanged($this, $ancienStatut, StatutCandidature::VALIDE));
            }
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
            
            // Envoyer une notification email si le candidat a un compte
            $candidat = \App\Models\Candidat::where('email', $this->email)->first();
            if ($candidat) {
                $candidat->notify(new \App\Notifications\CandidatureStatusChanged($this, $ancienStatut, StatutCandidature::REJETE));
            }
        }

        return $saved;
    }

    /**
     * Obtenir les directions disponibles
     */
    public static function getDirectionsDisponibles(): array
    {
        // Utiliser les configurations si disponibles, sinon fallback
        $configurationsDisponibles = ConfigurationListe::getOptions(ConfigurationListe::TYPE_DIRECTION);
        
        if (!empty($configurationsDisponibles)) {
            return $configurationsDisponibles;
        }

        // Fallback vers les valeurs en dur
        return [
            'direction_generale' => 'Direction Générale',
            'direction_financiere' => 'Direction Financière et Comptable',
            'direction_rh' => 'Direction des Ressources Humaines',
            'direction_marketing' => 'Direction Marketing et Communication',
            'direction_commerciale' => 'Direction Commerciale',
            'direction_production' => 'Direction de Production',
            'direction_technique' => 'Direction Technique',
            'direction_qualite' => 'Direction Qualité',
            'direction_logistique' => 'Direction Logistique',
            'direction_informatique' => 'Direction Informatique',
            'direction_juridique' => 'Direction Juridique',
            'direction_audit' => 'Direction Audit Interne',
        ];
    }

    /**
     * Obtenir les établissements référencés
     */
    public static function getEtablissements(): array
    {
        // Utiliser les configurations si disponibles, sinon fallback
        $configurationsDisponibles = ConfigurationListe::getOptions(ConfigurationListe::TYPE_ETABLISSEMENT);
        
        if (!empty($configurationsDisponibles)) {
            return $configurationsDisponibles;
        }

        // Fallback vers les valeurs en dur
        return [
            'unikin' => 'Université de Kinshasa (UNIKIN)',
            'ulk' => 'Université Libre de Kinshasa (ULK)',
            'upc' => 'Université Protestante du Congo (UPC)',
            'isc' => 'Institut Supérieur de Commerce (ISC)',
            'ista' => 'Institut Supérieur de Techniques Appliquées (ISTA)',
            'esg' => 'École Supérieure de Gestion (ESG)',
            'isp' => 'Institut Supérieur Pédagogique (ISP)',
            'upn' => 'Université Pédagogique Nationale (UPN)',
            'ifasic' => 'Institut Facultaire des Sciences de l\'Information et de la Communication (IFASIC)',
            'esii' => 'École Supérieure des Ingénieurs Industriels (ESII)',
            'autres' => 'Autres',
        ];
    }

    /**
     * Obtenir les niveaux d'étude
     */
    public static function getNiveauxEtude(): array
    {
        // Utiliser les configurations si disponibles, sinon fallback
        $configurationsDisponibles = ConfigurationListe::getOptions(ConfigurationListe::TYPE_NIVEAU_ETUDE);
        
        if (!empty($configurationsDisponibles)) {
            return $configurationsDisponibles;
        }

        // Fallback vers les valeurs en dur
        return [
            'ecole_secondaire' => 'École Secondaire',
            'bac_1' => 'Première année (Bac+1)',
            'bac_2' => 'Deuxième année (Bac+2)',
            'bac_3' => 'Licence/Graduat (Bac+3)',
            'bac_4' => 'Maîtrise (Bac+4)',
            'bac_5' => 'Master (Bac+5)',
            'doctorat' => 'Doctorat/PhD',
        ];
    }

    /**
     * Obtenir les postes disponibles pour les stages
     */
    public static function getPostesDisponibles(): array
    {
        // Utiliser les configurations si disponibles, sinon fallback
        $configurationsDisponibles = ConfigurationListe::getOptions(ConfigurationListe::TYPE_POSTE);
        
        if (!empty($configurationsDisponibles)) {
            return $configurationsDisponibles;
        }

        // Fallback vers les valeurs en dur
        return [
            'assistant_commercial' => 'Stagiaire Assistant(e) Commercial(e)',
            'assistant_marketing' => 'Stagiaire Assistant(e) Marketing',
            'assistant_communication' => 'Stagiaire Assistant(e) Communication',
            'assistant_comptable' => 'Stagiaire Assistant(e) Comptable',
            'assistant_financier' => 'Stagiaire Assistant(e) Financier(ère)',
            'assistant_rh' => 'Stagiaire Assistant(e) RH',
            'assistant_production' => 'Stagiaire Assistant(e) Production',
            'assistant_qualite' => 'Stagiaire Assistant(e) Qualité',
            'assistant_logistique' => 'Stagiaire Assistant(e) Logistique',
            'assistant_technique' => 'Stagiaire Assistant(e) Technique',
            'assistant_informatique' => 'Stagiaire Assistant(e) Informatique',
            'assistant_juridique' => 'Stagiaire Assistant(e) Juridique',
            'assistant_audit' => 'Stagiaire Assistant(e) Audit',
            'developpeur' => 'Stagiaire Développeur(euse)',
            'analyste_donnees' => 'Stagiaire Analyste de Données',
            'chef_projet_junior' => 'Stagiaire Chef de Projet Junior',
            'assistant_direction' => 'Stagiaire Assistant(e) Direction',
            'autre_poste' => 'Autre poste (à préciser)',
        ];
    }
} 