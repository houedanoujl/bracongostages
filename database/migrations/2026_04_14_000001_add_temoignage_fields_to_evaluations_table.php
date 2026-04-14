<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fusion Témoignages → Retours d'expérience
     * Ajoute les champs témoignage à la table evaluations et supprime la table temoignages.
     */
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->text('temoignage_texte')->nullable()->after('note_moyenne');
            $table->text('citation_accueil')->nullable()->after('temoignage_texte');
            $table->string('photo')->nullable()->after('citation_accueil');
            $table->integer('note_experience')->nullable()->default(5)->after('photo');
            $table->json('competences_tags')->nullable()->after('note_experience');
            $table->boolean('afficher_en_accueil')->default(false)->after('competences_tags');
            $table->integer('ordre_affichage')->default(0)->after('afficher_en_accueil');
        });

        Schema::dropIfExists('temoignages');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'temoignage_texte',
                'citation_accueil',
                'photo',
                'note_experience',
                'competences_tags',
                'afficher_en_accueil',
                'ordre_affichage',
            ]);
        });

        // Note: la table temoignages ne sera pas recréée automatiquement
        // Sa migration de création reste dans l'historique
    }
};
