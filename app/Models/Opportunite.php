<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Opportunite extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'slug',
        'description',
        'description_longue',
        'categorie',
        'niveau_requis',
        'duree',
        'competences_requises',
        'competences_acquises',
        'places_disponibles',
        'actif',
        'date_debut_publication',
        'date_fin_publication',
        'icone',
        'ordre_affichage',
        'directions_associees',
    ];

    protected $casts = [
        'competences_requises' => 'array',
        'competences_acquises' => 'array',
        'directions_associees' => 'array',
        'actif' => 'boolean',
        'places_disponibles' => 'integer',
        'ordre_affichage' => 'integer',
        'date_debut_publication' => 'date',
        'date_fin_publication' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($opportunite) {
            if (empty($opportunite->slug) && !empty($opportunite->titre)) {
                $opportunite->slug = Str::slug($opportunite->titre);
            }
        });

        static::updating(function ($opportunite) {
            if ($opportunite->isDirty('titre') && (empty($opportunite->slug) || $opportunite->getOriginal('slug') === Str::slug($opportunite->getOriginal('titre')))) {
                $opportunite->slug = Str::slug($opportunite->titre);
            }
        });
    }

    /**
     * Relation avec les candidatures
     */
    public function candidatures(): HasMany
    {
        return $this->hasMany(Candidature::class, 'opportunite_id', 'slug');
    }

    /**
     * Scope pour les opportunités actives
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les opportunités publiées
     */
    public function scopePubliee($query)
    {
        $today = now()->toDateString();
        return $query->where('actif', true)
            ->where(function ($query) use ($today) {
                $query->whereNull('date_debut_publication')
                      ->orWhere('date_debut_publication', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('date_fin_publication')
                      ->orWhere('date_fin_publication', '>=', $today);
            });
    }

    /**
     * Scope ordonné pour l'affichage
     */
    public function scopeOrdonne($query)
    {
        return $query->orderBy('ordre_affichage')->orderBy('titre');
    }

    /**
     * Scope par catégorie
     */
    public function scopeParCategorie($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    /**
     * Obtenir les catégories disponibles
     */
    public static function getCategories(): array
    {
        return [
            'technique' => 'Technique',
            'commercial' => 'Commercial',
            'administratif' => 'Administratif',
            'production' => 'Production',
            'finance' => 'Finance',
            'rh' => 'Ressources Humaines',
        ];
    }

    /**
     * Obtenir les niveaux requis
     */
    public static function getNiveauxRequis(): array
    {
        return [
            'ecole_secondaire' => 'École Secondaire',
            'bac_1' => 'Bac+1',
            'bac_2' => 'Bac+2',
            'bac_3' => 'Bac+3',
            'bac_4' => 'Bac+4',
            'bac_5' => 'Bac+5',
            'doctorat' => 'Doctorat',
            'tous_niveaux' => 'Tous niveaux',
        ];
    }

    /**
     * Obtenir les durées possibles
     */
    public static function getDurees(): array
    {
        return [
            '1-2 mois' => '1-2 mois',
            '2-3 mois' => '2-3 mois',
            '3-4 mois' => '3-4 mois',
            '3-6 mois' => '3-6 mois',
            '4-6 mois' => '4-6 mois',
            '6+ mois' => '6+ mois',
        ];
    }

    /**
     * Vérifier si l'opportunité est disponible
     */
    public function estDisponible(): bool
    {
        if (!$this->actif) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->date_debut_publication && $this->date_debut_publication > $today) {
            return false;
        }

        if ($this->date_fin_publication && $this->date_fin_publication < $today) {
            return false;
        }

        return true;
    }

    /**
     * Obtenir le nombre de candidatures reçues
     */
    public function getNombreCandidaturesAttribute(): int
    {
        return $this->candidatures()->count();
    }

    /**
     * Obtenir le nombre de places restantes
     */
    public function getPlacesRestantesAttribute(): int
    {
        $candidaturesValidees = $this->candidatures()
            ->where('statut', \App\Enums\StatutCandidature::VALIDE)
            ->count();
        
        return max(0, $this->places_disponibles - $candidaturesValidees);
    }

    /**
     * Obtenir la couleur de la catégorie
     */
    public function getCouleurCategorieAttribute(): string
    {
        return match ($this->categorie) {
            'technique' => 'blue',
            'commercial' => 'green',
            'administratif' => 'purple',
            'production' => 'orange',
            'finance' => 'red',
            'rh' => 'pink',
            default => 'gray',
        };
    }

    /**
     * Obtenir toutes les opportunités pour les options de sélection
     */
    public static function getOpportuniteOptions(): array
    {
        return static::publiee()
            ->ordonne()
            ->pluck('titre', 'slug')
            ->toArray();
    }
}
