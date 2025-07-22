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
        Schema::create('documents_candidat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidat_id')->constrained('candidats')->onDelete('cascade');
            $table->string('type_document'); // cv, lettre_motivation, certificat_scolarite, etc.
            $table->string('nom_original');
            $table->string('chemin_fichier');
            $table->unsignedBigInteger('taille_fichier')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();
            
            $table->index(['candidat_id', 'type_document']);
            $table->unique(['candidat_id', 'type_document']); // Un seul document par type par candidat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_candidat');
    }
};
