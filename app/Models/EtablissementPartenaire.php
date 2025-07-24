<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtablissementPartenaire extends Model
{
    use HasFactory;

    protected $table = 'etablissement_partenaires';

    protected $fillable = [
        'nom',
        'logo',
        'url',
        'ordre',
        'actif',
    ];
    
    /**
     * Get the logo URL attribute
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }
        
        return url('/uploads/' . $this->logo);
    }
}
