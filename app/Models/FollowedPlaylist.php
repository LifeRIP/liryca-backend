<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowedPlaylist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'playlist_id', 'follow_date'];

    /**
     * Relación con el modelo User.
     * Un FollowedPlaylist pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el modelo Playlist.
     * Un FollowedPlaylist pertenece a una playlist.
     */
    public function playlist()
    {
        return $this->belongsTo(Playlist::class, 'playlist_id');
    }
}
