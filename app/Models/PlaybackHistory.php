<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaybackHistory extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Clave primaria (autoincremental)
    public $incrementing = true; // Es un campo autoincremental
    protected $fillable = ['user_id', 'song_id', 'play_date', 'is_active']; // Atributos que pueden ser asignados en masa

    /**
     * Relación con el modelo User.
     * Un historial de reproducción pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el modelo Song.
     * Un historial de reproducción pertenece a una canción.
     */
    public function song()
    {
        return $this->belongsTo(Song::class, 'song_id');
    }
}
