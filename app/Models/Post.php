<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Clave primaria (autoincremental)
    public $incrementing = true; // Es un campo autoincremental
    protected $fillable = ['user_id', 'content', 'song_id', 'album_id', 'artist_id', 'playlist_id', 'action_type', 'is_active'];

    /**
     * Relación con el modelo User.
     * Un post pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el modelo Song.
     * Un post está asociado a una canción.
     */
    public function song()
    {
        return $this->belongsTo(Song::class, 'song_id');
    }

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    public function playlist()
    {
        return $this->belongsTo(Playlist::class, 'playlist_id');
    }

    // Validación para asegurar que solo un campo esté presente
    public function save(array $options = [])
    {
        if ($this->song_id && ($this->album_id || $this->artist_id || $this->playlist_id)) {
            throw new \Exception('Solo se puede asociar un campo a la vez');
        }
        if ($this->album_id && ($this->song_id || $this->artist_id || $this->playlist_id)) {
            throw new \Exception('Solo se puede asociar un campo a la vez');
        }
        if ($this->artist_id && ($this->song_id || $this->album_id || $this->playlist_id)) {
            throw new \Exception('Solo se puede asociar un campo a la vez');
        }
        if ($this->playlist_id && ($this->song_id || $this->album_id || $this->artist_id)) {
            throw new \Exception('Solo se puede asociar un campo a la vez');
        }

        parent::save($options);
    }

    // Relación con los likes de los posts
    public function postlikes()
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    /**
     * Relación con el modelo Comment.
     * Un post puede tener muchos comentarios.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }
}
