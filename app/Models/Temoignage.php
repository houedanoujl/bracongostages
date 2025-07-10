<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Temoignage extends Model
{
    use HasFactory;

    protected $table = 'temoignages';

    protected $fillable = [
        'nom',
        'prenom',
        'poste_occupe',
        'entreprise',
        'etablissement_origine',
        'photo',
        'temoignage',
        'citation_courte',
        'date_stage_debut',
        'date_stage_fin',
        'duree_stage',
        'direction_stage',
        'actif',
        'mis_en_avant',
        'ordre_affichage',
        'note_experience',
        'competences_acquises',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'mis_en_avant' => 'boolean',
        'ordre_affichage' => 'integer',
        'note_experience' => 'integer',
        'competences_acquises' => 'array',
        'date_stage_debut' => 'date',
        'date_stage_fin' => 'date',
    ];

    /**
     * Scope pour les témoignages actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les témoignages mis en avant
     */
    public function scopeMisEnAvant($query)
    {
        return $query->where('mis_en_avant', true);
    }

    /**
     * Scope ordonné pour l'affichage
     */
    public function scopeOrdonne($query)
    {
        return $query->orderBy('ordre_affichage')->orderBy('created_at', 'desc');
    }

    /**
     * Scope par établissement
     */
    public function scopeParEtablissement($query, string $etablissement)
    {
        return $query->where('etablissement_origine', $etablissement);
    }

    /**
     * Accessor pour le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Accessor pour l'URL de la photo
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo; // URL externe
        }

        return Storage::url($this->photo); // Fichier local
    }

    /**
     * Accessor pour la durée du stage formatée
     */
    public function getDureeStageFormatteeAttribute(): ?string
    {
        if ($this->duree_stage) {
            return $this->duree_stage;
        }

        if ($this->date_stage_debut && $this->date_stage_fin) {
            $diff = $this->date_stage_debut->diffInMonths($this->date_stage_fin);
            return $diff . ' mois';
        }

        return null;
    }

    /**
     * Accessor pour les étoiles d'évaluation
     */
    public function getEtoilesAttribute(): string
    {
        return str_repeat('⭐', $this->note_experience);
    }

    /**
     * Obtenir les témoignages pour la homepage
     */
    public static function pourHomepage(int $limite = 3)
    {
        return static::actif()
            ->misEnAvant()
            ->ordonne()
            ->limit($limite)
            ->get();
    }

    /**
     * Obtenir tous les témoignages actifs pour la page dédiée
     */
    public static function tousActifs()
    {
        return static::actif()
            ->ordonne()
            ->get();
    }

    /**
     * Obtenir les établissements représentés dans les témoignages
     */
    public static function getEtablissementsRepresentes(): array
    {
        return static::actif()
            ->whereNotNull('etablissement_origine')
            ->pluck('etablissement_origine')
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Obtenir les directions de stage représentées
     */
    public static function getDirectionsRepresentees(): array
    {
        return static::actif()
            ->whereNotNull('direction_stage')
            ->pluck('direction_stage')
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Calculer la note moyenne des expériences
     */
    public static function noteMoyenne(): float
    {
        return static::actif()->avg('note_experience') ?? 0;
    }

    /**
     * Boot method pour définir l'ordre automatiquement
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($temoignage) {
            if (is_null($temoignage->ordre_affichage)) {
                $temoignage->ordre_affichage = static::max('ordre_affichage') + 1;
            }
        });
    }
}
