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
        Schema::create('statistique_accueils', function (Blueprint $table) {
            $table->id();
            $table->string('cle')->unique(); // ex: total_candidatures
            $table->string('valeur'); // ex: 150
            $table->string('label'); // ex: Stagiaires par an
            $table->string('icone')->nullable(); // emoji ou classe css
            $table->integer('ordre')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistique_accueils');
    }
};
