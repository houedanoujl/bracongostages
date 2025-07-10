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
        Schema::create('configurations_listes', function (Blueprint $table) {
            $table->id();
            
            // Type de liste (etablissement, niveau_etude, direction, poste)
            $table->enum('type_liste', [
                'etablissement',
                'niveau_etude', 
                'direction',
                'poste'
            ]);
            
            // Valeur utilisée en interne (clé)
            $table->string('valeur');
            
            // Libellé affiché à l'utilisateur
            $table->string('libelle');
            
            // Description optionnelle
            $table->text('description')->nullable();
            
            // Ordre d'affichage
            $table->integer('ordre')->default(0);
            
            // Statut actif/inactif
            $table->boolean('actif')->default(true);
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['type_liste', 'actif', 'ordre']);
            $table->index('type_liste');
            
            // Contrainte unique sur type_liste + valeur
            $table->unique(['type_liste', 'valeur']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations_listes');
    }
};
