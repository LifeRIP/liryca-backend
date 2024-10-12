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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->foreignId('song_id')->nullable()->constrained('songs')->onDelete('cascade');
            $table->foreignId('album_id')->nullable()->constrained('albums')->onDelete('cascade');
            $table->foreignId('artist_id')->nullable()->constrained('artists')->onDelete('cascade');
            $table->foreignId('playlist_id')->nullable()->constrained('playlists')->onDelete('cascade');
            $table->enum('action_type', ['shared', 'recommended', 'not_recommended'])->default('shared');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
