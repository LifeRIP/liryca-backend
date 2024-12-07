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

        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('title')->collation('utf8_general_ci');
            $table->foreignId('artist_id')->constrained('artists')->onDelete('cascade');
            $table->date('release_date');
            $table->text('description')->nullable()->collation('utf8_general_ci');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
