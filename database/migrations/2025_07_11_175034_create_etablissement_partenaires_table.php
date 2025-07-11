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
        Schema::create('etablissement_partenaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('logo')->nullable(); // chemin ou url du logo
            $table->string('url')->nullable(); // lien vers le site partenaire
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
        Schema::dropIfExists('etablissement_partenaires');
    }
};
