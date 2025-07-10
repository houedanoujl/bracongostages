<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurationListe extends Model
{
    use HasFactory;

    protected $table = 'configurations_listes';

    protected $fillable = [
        'type_liste',
        'valeur',
        'libelle',
        'description',
        'ordre',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'ordre' => 'integer',
    ];

    // Types de listes disponibles
    const TYPE_ETABLISSEMENT = 'etablissement';
    const TYPE_NIVEAU_ETUDE = 'niveau_etude';
    const TYPE_DIRECTION = 'direction';
    const TYPE_POSTE = 'poste';

    /**
     * Scope pour filtrer par type de liste
     */
    public function scopeParType($query, string $type)
    {
        return $query->where('type_liste', $type);
    }

    /**
     * Scope pour les éléments actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope ordonné
     */
    public function scopeOrdonne($query)
    {
        return $query->orderBy('ordre')->orderBy('libelle');
    }

    /**
     * Obtenir les options pour un type de liste donné
     */
    public static function getOptions(string $type): array
    {
        return static::parType($type)
            ->actifs()
            ->ordonne()
            ->pluck('libelle', 'valeur')
            ->toArray();
    }

    /**
     * Obtenir tous les types de listes disponibles
     */
    public static function getTypesListes(): array
    {
        return [
            self::TYPE_ETABLISSEMENT => 'Établissements',
            self::TYPE_NIVEAU_ETUDE => 'Niveaux d\'étude',
            self::TYPE_DIRECTION => 'Directions',
            self::TYPE_POSTE => 'Postes disponibles',
        ];
    }

    /**
     * Boot method pour définir l'ordre automatiquement
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (is_null($model->ordre)) {
                $maxOrdre = static::parType($model->type_liste)->max('ordre') ?? 0;
                $model->ordre = $maxOrdre + 1;
            }
        });
    }
} 