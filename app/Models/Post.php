<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Clave primaria (autoincremental)
    public $incrementing = true; // Es un campo autoincremental
    protected $fillable = ['user_id', 'content', 'publishing_date', 'image', 'song_id']; // Atributos que pueden ser asignados en masa

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

    /**
     * Relación con el modelo Like.
     * Un post puede tener muchos likes.
     */
    public function likes()
    {
        return $this->hasMany(Like::class, 'post_id');
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
