<?php

namespace Database\Seeders;

use App\Models\EtablissementPartenaire;
use Illuminate\Database\Seeder;

class EtablissementPartenaireSeeder extends Seeder
{
    public function run(): void
    {
        $etablissements = [
            [
                'nom' => 'Université de Lomé',
                'logo' => 'logos/universite-lome.png',
                'url' => 'https://www.univ-lome.tg',
                'ordre' => 1,
                'actif' => true,
            ],
            [
                'nom' => 'École Nationale d\'Administration',
                'logo' => 'logos/ena-togo.png',
                'url' => 'https://www.ena.tg',
                'ordre' => 2,
                'actif' => true,
            ],
            [
                'nom' => 'Institut Supérieur de Commerce',
                'logo' => 'logos/isc-lome.png',
                'url' => 'https://www.isc-lome.tg',
                'ordre' => 3,
                'actif' => true,
            ],
            [
                'nom' => 'Centre de Formation Professionnelle',
                'logo' => 'logos/cfp-togo.png',
                'url' => 'https://www.cfp-togo.org',
                'ordre' => 4,
                'actif' => true,
            ],
            [
                'nom' => 'Académie des Sciences et Technologies',
                'logo' => 'logos/ast-togo.png',
                'url' => 'https://www.ast-togo.edu',
                'ordre' => 5,
                'actif' => true,
            ],
        ];

        foreach ($etablissements as $etablissement) {
            EtablissementPartenaire::create($etablissement);
        }
    }
}