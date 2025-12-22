<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DocumentCandidat extends Model
{
    use HasFactory;

    protected $table = 'documents_candidat';

    protected $fillable = [
        'candidat_id',
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
     * Relation avec le candidat
     */
    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
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
     * Vérifier si le fichier existe
     */
    public function fichierExiste(): bool
    {
        // Vérifier d'abord dans le disque public (où les fichiers sont stockés)
        if (Storage::disk('public')->exists($this->chemin_fichier)) {
            return true;
        }
        
        // Vérifier aussi dans le dossier documents_candidat/ du disque public
        $cheminAlternatif = 'documents_candidat/' . basename($this->chemin_fichier);
        if (Storage::disk('public')->exists($cheminAlternatif)) {
            return true;
        }
        
        return false;
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
        
        // Vérifier dans le dossier documents_candidat/ du disque public
        $cheminAlternatif = 'documents_candidat/' . basename($this->chemin_fichier);
        if (Storage::disk('public')->exists($cheminAlternatif)) {
            return $cheminAlternatif;
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
            return Storage::disk('public')->delete($cheminReel);
        }
        return true;
    }

    /**
     * Obtenir les types de documents autorisés
     */
    public static function getTypesDocument(): array
    {
        return [
            'cv' => 'Curriculum Vitae',
            'lettre_motivation' => 'Lettre de motivation',
            'certificat_scolarite' => 'Certificat de scolarité',
            'releves_notes' => 'Relevés de notes',
            'lettres_recommandation' => 'Lettres de recommandation',
            'certificats_competences' => 'Certificats de compétences',
        ];
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
}
