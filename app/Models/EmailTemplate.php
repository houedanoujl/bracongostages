<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'slug',
        'nom',
        'sujet',
        'contenu',
        'placeholders_disponibles',
        'actif',
    ];

    protected $casts = [
        'placeholders_disponibles' => 'array',
        'actif' => 'boolean',
    ];

    public static function getTemplate(string $slug): self
    {
        return static::where('slug', $slug)->where('actif', true)->firstOrFail();
    }

    public function remplacerPlaceholders(Candidature $candidature, array $extras = []): array
    {
        $directions = Candidature::getDirectionsDisponibles();
        $serviceAffecte = $candidature->service_affecte;
        $directionLabel = $directions[$serviceAffecte] ?? $serviceAffecte ?? '';

        $replacements = [
            '{nom}' => $candidature->nom,
            '{prenom}' => $candidature->prenom,
            '{email}' => $candidature->email,
            '{date_test}' => $candidature->date_test ? \Carbon\Carbon::parse($candidature->date_test)->format('d/m/Y') : '',
            '{date_debut}' => $candidature->date_debut_stage ? \Carbon\Carbon::parse($candidature->date_debut_stage)->format('d/m/Y') : '',
            '{date_fin}' => $candidature->date_fin_stage ? \Carbon\Carbon::parse($candidature->date_fin_stage)->format('d/m/Y') : '',
            '{direction_service}' => $directionLabel,
            '{etablissement}' => $candidature->etablissement ?? '',
            '{code_suivi}' => $candidature->code_suivi ?? '',
        ];

        // Merge extras (e.g. heure_test, heure_presentation)
        foreach ($extras as $key => $value) {
            $replacements['{' . $key . '}'] = $value;
        }

        $sujet = str_replace(array_keys($replacements), array_values($replacements), $this->sujet);
        $contenu = str_replace(array_keys($replacements), array_values($replacements), $this->contenu);

        return [
            'sujet' => $sujet,
            'contenu' => $contenu,
        ];
    }
}
