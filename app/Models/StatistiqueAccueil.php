<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatistiqueAccueil extends Model
{
    use HasFactory;

    protected $table = 'statistique_accueils';

    protected $fillable = [
        'cle',
        'valeur',
        'label',
        'icone',
        'ordre',
        'actif',
    ];
}
