<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FollowAlbum extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'album_id'];

    /**
     * Relación con el modelo User.
     * Un FollowAlbum pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el modelo Album.
     * Un FollowAlbum pertenece a un album.
     */

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }
}
