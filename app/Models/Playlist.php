<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Clave primaria (autoincremental)
    public $incrementing = true; // Es un campo autoincremental
    protected $fillable = ['name', 'user_id', 'description', 'privacy', 'image', 'is_active']; // Atributos que pueden ser asignados en masa

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
     * Una playlist puede tener muchas canciones a través de la tabla pivot PlaylistSong.
     */
    public function songs()
    {
        return $this->belongsToMany(Song::class, 'playlist_songs', 'playlist_id', 'song_id');
    }

    /**
     * Relación con el modelo SharedPlaylist.
     * Una playlist puede ser compartida con otros usuarios.
     */
    public function sharedPlaylists()
    {
        return $this->hasMany(SharedPlaylist::class, 'playlist_id');
    }

    /**
     * Relación con el modelo Post.
     * Una playlist puede estar asociada a varios post.
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'playlist_id');
    }

    /**
     * Relación con el modelo FollowedPlaylist.
     * Una playlist puede ser seguida por varios usuarios.
     */
    public function followedPlaylists()
    {
        return $this->hasMany(FollowedPlaylist::class, 'playlist_id');
    }
}
