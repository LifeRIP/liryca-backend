<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaylistSong extends Model
{
    use HasFactory;

    public $incrementing = false; // No tiene un ID propio, ya que la clave es compuesta
    protected $primaryKey = ['playlist_id', 'song_id']; // Clave primaria compuesta
    protected $fillable = ['playlist_id', 'song_id', 'add_by', 'is_active']; // Atributos que pueden ser asignados en masa

    /**
     * Relación con el modelo Playlist.
     * Una canción en la playlist pertenece a una playlist.
     */
    public function playlist()
    {
        return $this->belongsTo(Playlist::class, 'playlist_id');
    }

    /**
     * Relación con el modelo Song.
     * Una canción en la playlist pertenece a una canción.
     */
    public function song()
    {
        return $this->belongsTo(Song::class, 'song_id');
    }
}
