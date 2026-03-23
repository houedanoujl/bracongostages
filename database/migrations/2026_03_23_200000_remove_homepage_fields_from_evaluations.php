<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Supprime les champs d'approbation homepage de la table evaluations.
     * Seuls les témoignages doivent pouvoir être approuvés pour la homepage.
     */
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('evaluations', 'approuve_pour_affichage')) {
                $table->dropColumn('approuve_pour_affichage');
            }
            if (Schema::hasColumn('evaluations', 'citation_affichage')) {
                $table->dropColumn('citation_affichage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->boolean('approuve_pour_affichage')->default(false)->after('note_moyenne');
            $table->text('citation_affichage')->nullable()->after('approuve_pour_affichage');
        });
    }
};
