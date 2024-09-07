<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Clave primaria (autoincremental)
    public $incrementing = true; // Es un campo autoincremental
    protected $fillable = ['user_id', 'verified', 'about']; // Atributos que pueden ser asignados en masa

    /**
     * Relación con el modelo User.
     * Un artista pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el modelo Album.
     * Un artista puede tener muchos álbumes.
     */
    public function albums()
    {
        return $this->hasMany(Album::class, 'artistID');
    }

    /**
     * Relación con el modelo Song.
     * Un artista puede tener muchas canciones.
     */
    public function songs()
    {
        return $this->hasMany(Song::class, 'artistID');
    }
}
