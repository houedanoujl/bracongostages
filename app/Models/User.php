<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'direction',
        'is_active',
        'last_login_at',
        'est_tuteur',
        'poste',
        'competences_tuteur',
        'bio_tuteur',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'est_tuteur' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Relation: les candidatures dont cet utilisateur est tuteur
     */
    public function candidaturesTuterees()
    {
        return $this->hasMany(\App\Models\Candidature::class, 'tuteur_id');
    }

    /**
     * Déterminer si l'utilisateur peut accéder au panel Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->email;
    }

    /**
     * Scope pour les utilisateurs actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par direction
     */
    public function scopeParDirection($query, string $direction)
    {
        return $query->where('direction', $direction);
    }

    /**
     * Mettre à jour la dernière connexion
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
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
     * Vérifier si l'utilisateur est admin
     */
    public function isAdmin(): bool
    {
        return $this->direction === 'Direction des Ressources Humaines';
    }

    /**
     * Activer/désactiver l'utilisateur
     */
    public function toggleActive(): bool
    {
        $this->is_active = !$this->is_active;
        return $this->save();
    }

    /**
     * Obtenir le nom complet avec direction
     */
    public function getNomCompletAvecDirectionAttribute(): string
    {
        return $this->name . ($this->direction ? " ({$this->direction})" : '');
    }
} 