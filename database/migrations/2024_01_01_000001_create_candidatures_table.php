<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            
            // Informations personnelles
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone');
            $table->string('email')->nullable();
            
            // Informations académiques
            $table->string('etablissement');
            $table->string('niveau_etude');
            $table->string('faculte');
            $table->text('objectif_stage');
            
            // Préférences de stage
            $table->json('directions_souhaitees'); // Array de directions souhaitées
            $table->text('projets_souhaites')->nullable();
            $table->text('competences_souhaitees')->nullable();
            $table->date('periode_debut_souhaitee');
            $table->date('periode_fin_souhaitee');
            
            // Statut et traitement
            $table->enum('statut', [
                'non_traite',
                'analyse_dossier',
                'attente_test',
                'attente_resultats',
                'attente_affectation',
                'valide',
                'rejete'
            ])->default('non_traite');
            $table->text('motif_rejet')->nullable();
            
            // Dates de stage (si validé)
            $table->date('date_debut_stage')->nullable();
            $table->date('date_fin_stage')->nullable();
            
            // Code de suivi unique
            $table->string('code_suivi')->unique();
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['statut', 'created_at']);
            $table->index('etablissement');
            $table->index('niveau_etude');
            $table->index('code_suivi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
}; 