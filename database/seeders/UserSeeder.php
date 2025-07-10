<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utilisateur administrateur principal
        User::create([
            'name' => 'Administrateur BRACONGO',
            'email' => 'admin@bracongo.com',
            'password' => Hash::make('BracongoAdmin2024!'),
            'telephone' => '+243 81 000 0001',
            'direction' => 'Direction des Ressources Humaines',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Direction des Ressources Humaines
        User::create([
            'name' => 'Marie-Claire Kabongo',
            'email' => 'marie.kabongo@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 82 001 0001',
            'direction' => 'Direction des Ressources Humaines',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jean-Baptiste Mukendi',
            'email' => 'jean.mukendi@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 83 001 0002',
            'direction' => 'Direction des Ressources Humaines',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Direction de Production (Brasserie)
        User::create([
            'name' => 'Paul Nzeza',
            'email' => 'paul.nzeza@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 84 002 0001',
            'direction' => 'Direction de Production',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Thérèse Mputu',
            'email' => 'therese.mputu@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 85 002 0002',
            'direction' => 'Direction de Production',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Direction Marketing et Communication
        User::create([
            'name' => 'Grâce Mbuyi',
            'email' => 'grace.mbuyi@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 86 003 0001',
            'direction' => 'Direction Marketing et Communication',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Didier Lubaki',
            'email' => 'didier.lubaki@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 87 003 0002',
            'direction' => 'Direction Marketing et Communication',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Direction Financière et Comptable
        User::create([
            'name' => 'Pierre Kasongo',
            'email' => 'pierre.kasongo@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 88 004 0001',
            'direction' => 'Direction Financière et Comptable',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Chantal Nkomo',
            'email' => 'chantal.nkomo@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 89 004 0002',
            'direction' => 'Direction Financière et Comptable',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Direction Technique
        User::create([
            'name' => 'Emmanuel Tshiala',
            'email' => 'emmanuel.tshiala@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 90 005 0001',
            'direction' => 'Direction Technique',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Direction Qualité
        User::create([
            'name' => 'Ornella Mwanza',
            'email' => 'ornella.mwanza@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 91 006 0001',
            'direction' => 'Direction Qualité',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Direction Commerciale
        User::create([
            'name' => 'Serge Mbala',
            'email' => 'serge.mbala@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 92 007 0001',
            'direction' => 'Direction Commerciale',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Direction Logistique
        User::create([
            'name' => 'Jonathan Nzuzi',
            'email' => 'jonathan.nzuzi@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 93 008 0001',
            'direction' => 'Direction Logistique',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Direction Informatique
        User::create([
            'name' => 'Béatrice Kabila',
            'email' => 'beatrice.kabila@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 94 009 0001',
            'direction' => 'Direction Informatique',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Direction Juridique
        User::create([
            'name' => 'Patrick Mukendi',
            'email' => 'patrick.mukendi@bracongo.com',
            'password' => Hash::make('password123'),
            'telephone' => '+243 95 010 0001',
            'direction' => 'Direction Juridique',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Direction Générale
        User::create([
            'name' => 'Directeur Général BRACONGO',
            'email' => 'dg@bracongo.com',
            'password' => Hash::make('BracongoDG2024!'),
            'telephone' => '+243 81 000 0000',
            'direction' => 'Direction Générale',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('16 utilisateurs BRACONGO créés avec succès.');
        $this->command->info('🍺 Équipe BRACONGO prête pour la gestion des stages !');
        $this->command->info('');
        $this->command->info('📧 Comptes principaux :');
        $this->command->info('   Admin RH: admin@bracongo.com / BracongoAdmin2024!');
        $this->command->info('   DG: dg@bracongo.com / BracongoDG2024!');
        $this->command->info('   Autres: password123');
    }
} 