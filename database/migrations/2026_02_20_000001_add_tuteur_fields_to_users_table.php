<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'est_tuteur')) {
                $table->boolean('est_tuteur')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('users', 'poste')) {
                $table->string('poste')->nullable()->after('est_tuteur');
            }
            if (!Schema::hasColumn('users', 'competences_tuteur')) {
                $table->text('competences_tuteur')->nullable()->after('poste');
            }
            if (!Schema::hasColumn('users', 'bio_tuteur')) {
                $table->text('bio_tuteur')->nullable()->after('competences_tuteur');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['est_tuteur', 'poste', 'competences_tuteur', 'bio_tuteur']);
        });
    }
};
