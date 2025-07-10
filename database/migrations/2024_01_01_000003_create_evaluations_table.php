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
            
            // Relation avec la candidature (1-1)
            $table->foreignId('candidature_id')->unique()->constrained()->onDelete('cascade');
            
            // Notes de satisfaction (1-5)
            $table->tinyInteger('note_plateforme')->nullable()->comment('Note de 1 à 5 pour la plateforme');
            $table->tinyInteger('note_processus')->nullable()->comment('Note de 1 à 5 pour le processus');
            
            // Commentaires et suggestions
            $table->text('commentaires')->nullable();
            $table->boolean('recommandation')->nullable()->comment('Recommanderait-il BRACONGO ?');
            $table->text('suggestions_amelioration')->nullable();
            
            $table->timestamps();
            
            // Index pour les statistiques
            $table->index(['note_plateforme', 'note_processus']);
            $table->index('created_at');
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