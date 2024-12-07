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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->collation('utf8_general_ci');
            $table->foreignId('artist_id')->constrained('artists')->onDelete('cascade');
            $table->foreignId('album_id')->constrained('albums')->onDelete('cascade');
            $table->time('time');
            $table->string('genre')->collation('utf8_general_ci');
            $table->string('url_song');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
