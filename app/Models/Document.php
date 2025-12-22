<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidature_id',
        'type_document',
        'nom_original',
        'chemin_fichier',
        'taille_fichier',
        'mime_type',
    ];

    protected $casts = [
        'taille_fichier' => 'integer',
    ];

    /**
     * Relation avec la candidature
     */
    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }

    /**
     * Accessor pour l'URL du fichier
     */
    public function getUrlAttribute(): string
    {
        $cheminReel = $this->getCheminReel();
        if ($cheminReel) {
            return Storage::disk('public')->url($cheminReel);
        }
        return Storage::disk('public')->url($this->chemin_fichier);
    }

    /**
     * Accessor pour la taille formatée
     */
    public function getTailleFormateeAttribute(): string
    {
        $bytes = $this->taille_fichier;
        
        if ($bytes === 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Accessor pour l'icône du type de fichier
     */
    public function getIconeAttribute(): string
    {
        return match ($this->type_document) {
            'cv' => '📄',
            'lettre_motivation' => '📝',
            'lettre_recommandation' => '📋',
            'piece_identite' => '🆔',
            'diplome' => '🎓',
            'autre' => '📎',
            default => '📄',
        };
    }

    /**
     * Obtenir les types de documents autorisés
     */
    public static function getTypesDocument(): array
    {
        return [
            'cv' => 'Curriculum Vitae',
            'lettre_motivation' => 'Lettre de motivation',
            'lettre_recommandation' => 'Lettre de recommandation',
            'piece_identite' => 'Pièce d\'identité',
            'diplome' => 'Diplôme/Attestation',
            'autre' => 'Autre document',
        ];
    }

    /**
     * Obtenir les types MIME autorisés
     */
    public static function getMimeTypesAutorises(): array
    {
        return [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'image/jpg',
        ];
    }

    /**
     * Obtenir les extensions autorisées
     */
    public static function getExtensionsAutorisees(): array
    {
        return ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    }

    /**
     * Vérifier si le fichier existe encore
     */
    public function fichierExiste(): bool
    {
        // Vérifier d'abord dans le disque public (où les fichiers sont stockés)
        if (Storage::disk('public')->exists($this->chemin_fichier)) {
            return true;
        }
        
        // Vérifier aussi dans le dossier documents/ du disque public
        $cheminAlternatif = 'documents/' . basename($this->chemin_fichier);
        if (Storage::disk('public')->exists($cheminAlternatif)) {
            return true;
        }
        
        // Vérifier dans le disque par défaut en dernier recours
        return Storage::exists($this->chemin_fichier);
    }

    /**
     * Obtenir le chemin réel du fichier (corrige les chemins)
     */
    public function getCheminReel(): ?string
    {
        // Vérifier d'abord le chemin original dans le disque public
        if (Storage::disk('public')->exists($this->chemin_fichier)) {
            return $this->chemin_fichier;
        }
        
        // Vérifier dans le dossier documents/ du disque public
        $cheminAlternatif = 'documents/' . basename($this->chemin_fichier);
        if (Storage::disk('public')->exists($cheminAlternatif)) {
            return $cheminAlternatif;
        }
        
        // Vérifier dans le disque par défaut
        if (Storage::exists($this->chemin_fichier)) {
            return $this->chemin_fichier;
        }
        
        return null;
    }

    /**
     * Supprimer le fichier du storage
     */
    public function supprimerFichier(): bool
    {
        $cheminReel = $this->getCheminReel();
        
        if ($cheminReel) {
            // Essayer de supprimer du disque public
            if (Storage::disk('public')->exists($cheminReel)) {
                return Storage::disk('public')->delete($cheminReel);
            }
            // Sinon du disque par défaut
            if (Storage::exists($cheminReel)) {
                return Storage::delete($cheminReel);
            }
        }
        
        return true;
    }

    /**
     * Boot du modèle pour nettoyer les fichiers à la suppression
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            $document->supprimerFichier();
        });
    }

    /**
     * Scope pour filtrer par type de document
     */
    public function scopeParType($query, string $type)
    {
        return $query->where('type_document', $type);
    }

    /**
     * Scope pour les documents récents
     */
    public function scopeRecents($query, int $jours = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($jours));
    }
} 