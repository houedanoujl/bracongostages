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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            
            // Relation avec la candidature
            $table->foreignId('candidature_id')->constrained()->onDelete('cascade');
            
            // Informations du document
            $table->enum('type_document', [
                'cv',
                'lettre_motivation',
                'lettre_recommandation',
                'piece_identite',
                'diplome',
                'autre'
            ]);
            $table->string('nom_original'); // Nom original du fichier
            $table->string('chemin_fichier'); // Chemin de stockage
            $table->unsignedBigInteger('taille_fichier'); // Taille en bytes
            $table->string('mime_type'); // Type MIME du fichier
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['candidature_id', 'type_document']);
            $table->index('type_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
}; 