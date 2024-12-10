<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileChange extends Model
{
    // Especifica la tabla a la que este modelo corresponde
    protected $table = 'profile_changes';

    // Los campos que pueden ser asignados masivamente
    protected $fillable = [
        'user_id', 'old_data', 'new_data',
    ];

    // Si tu tabla no tiene las marcas de tiempo (timestamps), desactívalas aquí
    public $timestamps = true; // Si es necesario
}
