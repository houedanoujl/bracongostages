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

        $appreciations = [
            'excellent' => 'Excellent',
            'tres_bien' => 'Très bien',
            'bien' => 'Bien',
            'satisfaisant' => 'Satisfaisant',
            'insuffisant' => 'Insuffisant',
        ];

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
            '{note_evaluation}' => $candidature->note_evaluation ?? 'N/A',
            '{appreciation_tuteur}' => $appreciations[$candidature->appreciation_tuteur ?? ''] ?? ($candidature->appreciation_tuteur ?? 'N/A'),
        ];

        // Merge extras (e.g. heure_test, heure_presentation)
        foreach ($extras as $key => $value) {
            $replacements['{' . $key . '}'] = $value;
        }

        $sujet = str_replace(array_keys($replacements), array_values($replacements), $this->sujet);
        $contenu = str_replace(array_keys($replacements), array_values($replacements), $this->contenu);

        // Corriger le double encodage UTF-8 (ex: informÃ©(e) → informé(e))
        $sujet = self::fixUtf8($sujet);
        $contenu = self::fixUtf8($contenu);

        return [
            'sujet' => $sujet,
            'contenu' => $contenu,
        ];
    }

    /**
     * Corrige le double encodage UTF-8 (Ã© → é, Ã  → à, etc.)
     */
    private static function fixUtf8(string $text): string
    {
        // Détecter si le texte contient des séquences doublement encodées
        if (preg_match('/\xC3[\x80-\xBF]/', $text)) {
            $fixed = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
            if (mb_check_encoding($fixed, 'UTF-8')) {
                return $fixed;
            }
        }
        return $text;
    }
}
