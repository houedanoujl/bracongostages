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
        Schema::table('documents', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('type_document');
        });
        
        Schema::table('documents', function (Blueprint $table) {
            // Recreate the enum column with additional types
            $table->enum('type_document', [
                'cv',
                'lettre_motivation',
                'lettre_recommandation',
                'piece_identite',
                'diplome',
                'certificat_scolarite',
                'releves_notes',
                'lettres_recommandation',
                'certificats_competences',
                'autre'
            ])->after('candidature_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('type_document');
        });
        
        Schema::table('documents', function (Blueprint $table) {
            $table->enum('type_document', [
                'cv',
                'lettre_motivation',
                'lettre_recommandation',
                'piece_identite',
                'diplome',
                'autre'
            ])->after('candidature_id');
        });
    }
};
