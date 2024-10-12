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
        // Crear la tabla de colaboradores (featuring)
        Schema::create('song_collaborators', function (Blueprint $table) {
            $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');
            $table->foreignId('artist_id')->constrained('artists')->onDelete('cascade');
            $table->primary(['song_id', 'artist_id']);  // Clave primaria compuesta para evitar duplicados
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_collaborators');
    }
};
