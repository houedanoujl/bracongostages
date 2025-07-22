<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CandidatSeeder::class,
            ConfigurationListeSeeder::class,
            OpportuniteSeeder::class,
            TemoignageSeeder::class,
            EtablissementPartenaireSeeder::class,
            CandidatureSeeder::class,
            EvaluationSeeder::class,
        ]);
    }
} 