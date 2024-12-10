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
        Schema::create('profile_changes', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id'); // Usamos uuid en lugar de foreignId
            $table->text('old_data'); // Los datos anteriores del perfil
            $table->text('new_data'); // Los nuevos datos del perfil
            $table->timestamps(); // Marca cuándo se registró el cambio
    
            // Establecer la clave foránea
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_changes');
    }
};
