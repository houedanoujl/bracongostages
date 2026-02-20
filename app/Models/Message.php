<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidature_id',
        'sender_type',
        'sender_id',
        'contenu',
        'lu_at',
    ];

    protected $casts = [
        'lu_at' => 'datetime',
    ];

    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }

    public function sender(): MorphTo
    {
        return $this->morphTo('sender', 'sender_type', 'sender_id');
    }

    public function getSenderNameAttribute(): string
    {
        if ($this->sender_type === 'candidat') {
            $candidat = Candidat::find($this->sender_id);
            return $candidat ? $candidat->prenom . ' ' . $candidat->nom : 'Candidat';
        }

        $user = User::find($this->sender_id);
        return $user ? $user->name : 'Administration';
    }

    public function getIsFromAdminAttribute(): bool
    {
        return $this->sender_type === 'admin';
    }

    public function getIsReadAttribute(): bool
    {
        return $this->lu_at !== null;
    }

    public function markAsRead(): void
    {
        if (!$this->lu_at) {
            $this->update(['lu_at' => now()]);
        }
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('lu_at');
    }

    public function scopeForCandidature($query, int $candidatureId)
    {
        return $query->where('candidature_id', $candidatureId);
    }
}
