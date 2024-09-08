<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Clave primaria (autoincremental)
    public $incrementing = true; // Es un campo autoincremental
    protected $fillable = ['user_id', 'post_id', 'is_active']; // Atributos que pueden ser asignados en masa

    /**
     * Relación con el modelo User.
     * Un like pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el modelo Post.
     * Un like pertenece a un post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
