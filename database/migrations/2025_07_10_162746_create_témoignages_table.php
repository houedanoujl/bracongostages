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
        Schema::create('temoignages', function (Blueprint $table) {
            $table->id();
            
            // Informations de la personne
            $table->string('nom');
            $table->string('prenom');
            $table->string('poste_occupe');
            $table->string('entreprise')->default('BRACONGO');
            $table->string('etablissement_origine')->nullable();
            $table->string('photo')->nullable(); // Chemin vers la photo
            
            // Contenu du témoignage
            $table->text('temoignage');
            $table->text('citation_courte')->nullable(); // Citation mise en avant
            
            // Métadonnées
            $table->date('date_stage_debut')->nullable();
            $table->date('date_stage_fin')->nullable();
            $table->string('duree_stage')->nullable(); // ex: "3 mois", "6 mois"
            $table->string('direction_stage')->nullable();
            
            // Gestion de l'affichage
            $table->boolean('actif')->default(true);
            $table->boolean('mis_en_avant')->default(false); // Pour homepage
            $table->integer('ordre_affichage')->default(0);
            
            // Évaluation du stage
            $table->integer('note_experience')->default(5); // Note sur 5
            $table->json('competences_acquises')->nullable(); // Array des compétences
            
            $table->timestamps();
            
            // Index
            $table->index(['actif', 'mis_en_avant', 'ordre_affichage']);
            $table->index('etablissement_origine');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temoignages');
    }
};
