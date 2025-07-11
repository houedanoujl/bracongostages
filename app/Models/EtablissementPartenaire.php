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
}
