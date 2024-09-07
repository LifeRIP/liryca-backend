<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Clave primaria
    public $incrementing = true; // Es un campo autoincremental
    protected $fillable = ['title', 'artist_id', 'release_date', 'description', 'icon']; // Atributos que pueden ser asignados en masa

    /**
     * Relación con el modelo Artist.
     * Un álbum pertenece a un artista.
     */
    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    /**
     * Relación con el modelo Song.
     * Un álbum puede tener muchas canciones.
     */
    public function songs()
    {
        return $this->hasMany(Song::class, 'albumID');
    }
}