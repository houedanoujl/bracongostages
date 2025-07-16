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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained()->onDelete('cascade');
            
            // Satisfaction générale (1-5)
            $table->integer('satisfaction_generale')->nullable();
            
            // Recommandation
            $table->enum('recommandation', ['oui', 'peut_etre', 'non'])->nullable();
            
            // Environnement de travail
            $table->enum('accueil_integration', ['excellent', 'bon', 'moyen', 'insuffisant'])->nullable();
            $table->enum('encadrement_suivi', ['excellent', 'bon', 'moyen', 'insuffisant'])->nullable();
            $table->enum('conditions_travail', ['excellent', 'bon', 'moyen', 'insuffisant'])->nullable();
            $table->enum('ambiance_travail', ['excellent', 'bon', 'moyen', 'insuffisant'])->nullable();
            
            // Apprentissages
            $table->text('competences_developpees')->nullable();
            $table->text('reponse_attentes')->nullable();
            $table->text('aspects_enrichissants')->nullable();
            
            // Améliorations
            $table->text('suggestions_amelioration')->nullable();
            $table->enum('contact_futur', ['oui', 'non'])->nullable();
            
            // Commentaire libre
            $table->text('commentaire_libre')->nullable();
            
            // Note moyenne calculée automatiquement
            $table->decimal('note_moyenne', 3, 1)->nullable();
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['candidature_id', 'created_at']);
            $table->index('note_moyenne');
            $table->index('satisfaction_generale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
}; 