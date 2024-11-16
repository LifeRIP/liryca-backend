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
        return $this->hasMany(Album::class, 'artist_id');
    }

    /**
     * Relación con el modelo Song.
     * Un artista puede tener muchas canciones.
     */
    public function songs()
    {
        return $this->hasMany(Song::class, 'artist_id');
    }

    // Relacion uno a muchos con songs con el artista como colaborador
    public function songcollaborator()
    {
        return $this->hasMany(SongCollaborator::class, 'artist_id');
    }

    // Relación uno a muchos con los posts de las canciones del artista
    public function posts()
    {
        return $this->hasMany(Post::class, 'artist_id');
    }
}
