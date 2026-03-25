<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le suivi des emails envoyés par étape du wizard.
     * Structure JSON : { "Gestion": "2026-03-25T10:00:00", "Tests": null, ... }
     */
    public function up(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->json('emails_envoyes_par_etape')->nullable()->after('historique_statuts');
        });
    }

    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropColumn('emails_envoyes_par_etape');
        });
    }
};
