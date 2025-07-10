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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            
            // Clé unique pour identifier la configuration
            $table->string('cle')->unique();
            
            // Valeur de la configuration
            $table->text('valeur');
            
            // Type de la valeur (pour casting)
            $table->enum('type', ['string', 'integer', 'float', 'boolean', 'json', 'text'])->default('string');
            
            // Métadonnées pour l'interface d'administration
            $table->string('libelle'); // Nom affiché dans l'interface
            $table->text('description')->nullable(); // Description pour l'admin
            $table->string('groupe')->default('general'); // Regroupement (statistiques, seo, etc.)
            
            // Options pour l'interface
            $table->string('type_champ')->default('text'); // text, number, textarea, select, etc.
            $table->json('options_champ')->nullable(); // Options pour select, min/max pour number, etc.
            
            // Gestion
            $table->boolean('modifiable')->default(true); // Peut être modifié via l'interface
            $table->integer('ordre_affichage')->default(0);
            
            $table->timestamps();
            
            // Index
            $table->index(['groupe', 'ordre_affichage']);
            $table->index('modifiable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
