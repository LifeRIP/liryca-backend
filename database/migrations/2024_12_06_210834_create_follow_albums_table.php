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
        Schema::create('follow_albums', function (Blueprint $table) {
            $table->id(); // FollowAlbumID (PK)
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade'); // UsuarioID (FK)
            $table->foreignId('album_id')->constrained('albums')->onDelete('cascade'); // AlbumID (FK)
            $table->boolean('is_active')->default(true); // Activo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_albums');
    }
};
