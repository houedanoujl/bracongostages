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
     * Relation avec le CV
     */
    public function cv(): HasOne
    {
        return $this->hasOne(Document::class, 'candidature_id')
            ->where('type', 'cv')
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
        return !empty($this->cv_path);
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