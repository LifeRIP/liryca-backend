<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Clave primaria (autoincremental)
    public $incrementing = true; // Es un campo autoincremental
    protected $fillable = ['user_id', 'comment_id']; // Atributos que pueden ser asignados en masa

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el comentario
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
