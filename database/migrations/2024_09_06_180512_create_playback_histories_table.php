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
        Schema::create('playback_history', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');
            $table->timestamp('play_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playback_histories');
    }
};
