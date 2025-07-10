<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'cle',
        'valeur',
        'type',
        'libelle',
        'description',
        'groupe',
        'type_champ',
        'options_champ',
        'modifiable',
        'ordre_affichage',
    ];

    protected $casts = [
        'options_champ' => 'array',
        'modifiable' => 'boolean',
        'ordre_affichage' => 'integer',
    ];

    // Constantes pour les groupes
    const GROUPE_STATISTIQUES = 'statistiques';
    const GROUPE_SEO = 'seo';
    const GROUPE_CONTACT = 'contact';
    const GROUPE_GENERAL = 'general';

    // Constantes pour les types de champs
    const CHAMP_TEXT = 'text';
    const CHAMP_NUMBER = 'number';
    const CHAMP_TEXTAREA = 'textarea';
    const CHAMP_SELECT = 'select';
    const CHAMP_BOOLEAN = 'boolean';

    /**
     * Scope par groupe
     */
    public function scopeParGroupe($query, string $groupe)
    {
        return $query->where('groupe', $groupe);
    }

    /**
     * Scope pour les configurations modifiables
     */
    public function scopeModifiable($query)
    {
        return $query->where('modifiable', true);
    }

    /**
     * Scope ordonné
     */
    public function scopeOrdonne($query)
    {
        return $query->orderBy('ordre_affichage')->orderBy('libelle');
    }

    /**
     * Obtenir une valeur de configuration
     */
    public static function get(string $cle, $default = null)
    {
        return Cache::remember("config_{$cle}", 3600, function () use ($cle, $default) {
            $config = static::where('cle', $cle)->first();
            
            if (!$config) {
                return $default;
            }

            return static::castValue($config->valeur, $config->type);
        });
    }

    /**
     * Définir une valeur de configuration
     */
    public static function set(string $cle, $valeur, string $type = 'string'): void
    {
        $config = static::firstOrCreate(['cle' => $cle], [
            'libelle' => $cle,
            'type' => $type,
            'groupe' => self::GROUPE_GENERAL,
        ]);

        $config->update([
            'valeur' => is_array($valeur) || is_object($valeur) ? json_encode($valeur) : $valeur,
            'type' => $type,
        ]);

        Cache::forget("config_{$cle}");
    }

    /**
     * Obtenir toutes les configurations d'un groupe
     */
    public static function getGroupe(string $groupe): array
    {
        return Cache::remember("config_groupe_{$groupe}", 3600, function () use ($groupe) {
            return static::parGroupe($groupe)
                ->ordonne()
                ->get()
                ->mapWithKeys(function ($config) {
                    return [$config->cle => static::castValue($config->valeur, $config->type)];
                })
                ->toArray();
        });
    }

    /**
     * Obtenir les statistiques pour la homepage
     */
    public static function getStatistiques(): array
    {
        return static::getGroupe(self::GROUPE_STATISTIQUES);
    }

    /**
     * Caster une valeur selon son type
     */
    protected static function castValue($valeur, string $type)
    {
        return match ($type) {
            'integer' => (int) $valeur,
            'float' => (float) $valeur,
            'boolean' => filter_var($valeur, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($valeur, true),
            'text', 'string' => $valeur,
            default => $valeur,
        };
    }

    /**
     * Obtenir les groupes disponibles
     */
    public static function getGroupes(): array
    {
        return [
            self::GROUPE_STATISTIQUES => 'Statistiques Homepage',
            self::GROUPE_SEO => 'SEO & Métadonnées',
            self::GROUPE_CONTACT => 'Informations de Contact',
            self::GROUPE_GENERAL => 'Configuration Générale',
        ];
    }

    /**
     * Obtenir les types de champs disponibles
     */
    public static function getTypesChampsDisponibles(): array
    {
        return [
            self::CHAMP_TEXT => 'Texte court',
            self::CHAMP_NUMBER => 'Nombre',
            self::CHAMP_TEXTAREA => 'Texte long',
            self::CHAMP_SELECT => 'Liste déroulante',
            self::CHAMP_BOOLEAN => 'Oui/Non',
        ];
    }

    /**
     * Vider le cache des configurations
     */
    public static function clearCache(): void
    {
        $cles = static::pluck('cle');
        
        foreach ($cles as $cle) {
            Cache::forget("config_{$cle}");
        }

        foreach (array_keys(static::getGroupes()) as $groupe) {
            Cache::forget("config_groupe_{$groupe}");
        }
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($config) {
            Cache::forget("config_{$config->cle}");
            Cache::forget("config_groupe_{$config->groupe}");
        });

        static::deleted(function ($config) {
            Cache::forget("config_{$config->cle}");
            Cache::forget("config_groupe_{$config->groupe}");
        });
    }
}
