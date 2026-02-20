<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            if (!Schema::hasColumn('candidatures', 'resultat_test')) {
                $table->string('resultat_test')->nullable()->after('note_test');
            }
            if (!Schema::hasColumn('candidatures', 'decision_drh')) {
                $table->text('decision_drh')->nullable()->after('appreciation_tuteur');
            }
            if (!Schema::hasColumn('candidatures', 'date_reponse_recommandation')) {
                $table->date('date_reponse_recommandation')->nullable()->after('reponse_lettre_envoyee');
            }
            if (!Schema::hasColumn('candidatures', 'date_accueil_service')) {
                $table->date('date_accueil_service')->nullable()->after('induction_completee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropColumn([
                'resultat_test',
                'decision_drh',
                'date_reponse_recommandation',
                'date_accueil_service',
            ]);
        });
    }
};
