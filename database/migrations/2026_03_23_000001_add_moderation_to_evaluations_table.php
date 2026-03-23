<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->boolean('approuve_pour_affichage')->default(false)->after('note_moyenne');
            $table->text('citation_affichage')->nullable()->after('approuve_pour_affichage');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn(['approuve_pour_affichage', 'citation_affichage']);
        });
    }
};
