<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Candidat;
use Illuminate\Support\Facades\Hash;

class CandidatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidats = [
            [
                'nom' => 'Dupont',
                'prenom' => 'Marie',
                'email' => 'marie.dupont@example.com',
                'password' => Hash::make('password123'),
                'telephone' => '+243 123 456 789',
                'etablissement' => 'Université de Kinshasa',
                'niveau_etude' => 'Master',
                'faculte' => 'Sciences Économiques',
                'is_active' => true,
            ],
            [
                'nom' => 'Mukendi',
                'prenom' => 'Jean-Pierre',
                'email' => 'jean.mukendi@example.com',
                'password' => Hash::make('password123'),
                'telephone' => '+243 234 567 890',
                'etablissement' => 'Université Catholique du Congo',
                'niveau_etude' => 'Licence',
                'faculte' => 'Droit',
                'is_active' => true,
            ],
            [
                'nom' => 'Lumumba',
                'prenom' => 'Sophie',
                'email' => 'sophie.lumumba@example.com',
                'password' => Hash::make('password123'),
                'telephone' => '+243 345 678 901',
                'etablissement' => 'Institut Supérieur de Commerce',
                'niveau_etude' => 'Master',
                'faculte' => 'Gestion',
                'is_active' => true,
            ],
            [
                'nom' => 'Kabila',
                'prenom' => 'David',
                'email' => 'david.kabila@example.com',
                'password' => Hash::make('password123'),
                'telephone' => '+243 456 789 012',
                'etablissement' => 'Université Protestante au Congo',
                'niveau_etude' => 'Licence',
                'faculte' => 'Informatique',
                'is_active' => true,
            ],
            [
                'nom' => 'Mobutu',
                'prenom' => 'Claire',
                'email' => 'claire.mobutu@example.com',
                'password' => Hash::make('password123'),
                'telephone' => '+243 567 890 123',
                'etablissement' => 'Université de Lubumbashi',
                'niveau_etude' => 'Master',
                'faculte' => 'Médecine',
                'is_active' => true,
            ],
        ];

        foreach ($candidats as $candidatData) {
            Candidat::create($candidatData);
        }

        $this->command->info('Candidats de test créés avec succès !');
        $this->command->info('Emails de test :');
        foreach ($candidats as $candidat) {
            $this->command->info("- {$candidat['email']} (mot de passe: password123)");
        }
    }
}
