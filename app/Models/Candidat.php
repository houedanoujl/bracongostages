<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Candidat extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'etablissement',
        'niveau_etude',
        'faculte',
        'cv_path',
        'photo_path',
        'is_active',
        'email_verified_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Relation avec les candidatures
     */
    public function candidatures(): HasMany
    {
        return $this->hasMany(Candidature::class, 'email', 'email');
    }

    /**
     * Relation avec les documents du candidat
     */
    public function documentsCandidat(): HasMany
    {
        return $this->hasMany(DocumentCandidat::class);
    }

    /**
     * Relation avec le CV du profil
     */
    public function cvCandidat(): HasOne
    {
        return $this->hasOne(DocumentCandidat::class)
            ->where('type_document', 'cv')
            ->latest();
    }

    /**
     * Accessor pour le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Mettre à jour la dernière connexion
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Vérifier si le candidat a un CV
     */
    public function hasCv(): bool
    {
        return !empty($this->cv_path) || $this->cvCandidat()->exists();
    }

    /**
     * Obtenir un document par type
     */
    public function getDocumentByType(string $type): ?DocumentCandidat
    {
        $document = $this->documentsCandidat()->where('type_document', $type)->first();
        
        // Fallback : si pas de document_candidat pour le CV, vérifier cv_path
        if (!$document && $type === 'cv' && $this->cv_path) {
            // Créer un objet DocumentCandidat virtuel pour l'affichage
            $document = new DocumentCandidat([
                'candidat_id' => $this->id,
                'type_document' => 'cv',
                'nom_original' => basename($this->cv_path),
                'chemin_fichier' => $this->cv_path,
                'taille_fichier' => 0,
                'mime_type' => 'application/pdf',
            ]);
            $document->id = -1; // ID virtuel pour indiquer que ce n'est pas en base
        }
        
        return $document;
    }

    /**
     * Obtenir l'URL du CV
     */
    public function getCvUrlAttribute(): ?string
    {
        return $this->cv_path ? asset('storage/' . $this->cv_path) : null;
    }

    /**
     * Obtenir l'URL de la photo
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    /**
     * Scope pour les candidats actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('is_active', true);
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
     * Obtenir les candidatures récentes
     */
    public function getCandidaturesRecentesAttribute()
    {
        return $this->candidatures()->recent()->get();
    }

    /**
     * Obtenir la candidature active (non terminée)
     */
    public function getCandidatureActiveAttribute()
    {
        return $this->candidatures()
            ->whereNotIn('statut', [\App\Enums\StatutCandidature::VALIDE, \App\Enums\StatutCandidature::REJETE])
            ->latest()
            ->first();
    }

    /**
     * Vérifier si le candidat peut postuler
     */
    public function canPostuler(): bool
    {
        return $this->is_active && $this->hasCv();
    }

    /**
     * Route de notification pour les candidats
     */
    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }
} 