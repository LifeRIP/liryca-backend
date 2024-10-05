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
        Schema::create('followed_playlists', function (Blueprint $table) {
            $table->id(); // FollowedPlaylistID (PK)
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade'); // UsuarioID (FK)
            $table->foreignId('playlist_id')->constrained('playlists')->onDelete('cascade'); // PlaylistID (FK)
            $table->date('follow_date'); // FechaSeguimiento
            $table->boolean('is_active')->default(true); // Activo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followed_playlists');
    }
};
