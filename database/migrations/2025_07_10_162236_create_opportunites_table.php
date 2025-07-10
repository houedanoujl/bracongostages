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
        Schema::create('opportunites', function (Blueprint $table) {
            $table->id();
            
            // Informations de base
            $table->string('titre');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('description_longue')->nullable();
            
            // Métadonnées
            $table->string('categorie'); // technique, commercial, administratif
            $table->string('niveau_requis'); // Bac+2, Bac+3, etc.
            $table->string('duree'); // 3-6 mois, etc.
            $table->json('competences_requises')->nullable(); // Array de compétences
            $table->json('competences_acquises')->nullable(); // Array de compétences à acquérir
            
            // Gestion
            $table->integer('places_disponibles')->default(1);
            $table->boolean('actif')->default(true);
            $table->date('date_debut_publication')->nullable();
            $table->date('date_fin_publication')->nullable();
            
            // Informations supplémentaires
            $table->string('icone')->default('💼'); // Emoji ou classe CSS
            $table->integer('ordre_affichage')->default(0);
            $table->json('directions_associees')->nullable(); // Array des directions concernées
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['actif', 'ordre_affichage']);
            $table->index('categorie');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunites');
    }
};
