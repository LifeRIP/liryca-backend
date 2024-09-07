<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Clave primaria (autoincremental)
    public $incrementing = true; // Es un campo autoincremental
    protected $fillable = ['name', 'user_id', 'release_date', 'description', 'privacy', 'image']; // Atributos que pueden ser asignados en masa

    /**
     * Relación con el modelo User.
     * Una playlist pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el modelo Song.
     * Una playlist puede tener muchas canciones a través de la tabla pivot SongInPlaylist.
     */
    public function songs()
    {
        return $this->belongsToMany(Song::class, 'song_in_playlists', 'playlist_id', 'song_id')
            ->withPivot('date_added');
    }

    /**
     * Relación con el modelo SharedPlaylist.
     * Una playlist puede ser compartida con otros usuarios.
     */
    public function sharedPlaylists()
    {
        return $this->hasMany(SharedPlaylist::class, 'playlist_id');
    }
}
