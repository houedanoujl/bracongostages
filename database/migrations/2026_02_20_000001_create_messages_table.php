<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained()->onDelete('cascade');
            $table->string('sender_type'); // 'candidat' or 'admin'
            $table->unsignedBigInteger('sender_id');
            $table->text('contenu');
            $table->timestamp('lu_at')->nullable();
            $table->timestamps();

            $table->index(['candidature_id', 'created_at']);
            $table->index(['sender_type', 'sender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
