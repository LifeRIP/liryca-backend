<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Clave primaria (autoincremental)
    public $incrementing = true; // Es un campo autoincremental
    protected $fillable = ['title', 'artist_id', 'album_id', 'time', 'genre', 'url_song', 'is_active']; // Atributos que pueden ser asignados en masa

    /**
     * Relación con el modelo Artist.
     * Una canción pertenece a un artista.
     */
    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    /**
     * Relación con el modelo Album.
     * Una canción pertenece a un álbum.
     */
    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }

    /**
     * Relación con el modelo Post.
     * Una canción puede estar asociada a muchos posts.
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'song_id');
    }

    /**
     * Relación con el modelo PlaybackHistory.
     * Una canción puede aparecer en el historial de reproducción de muchos usuarios.
     */
    public function playbackHistories()
    {
        return $this->hasMany(PlaybackHistory::class, 'song_id');
    }
}
