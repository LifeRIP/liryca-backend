<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedPlaylist extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Clave primaria (autoincremental)
    public $incrementing = true; // Es un campo autoincremental
    protected $fillable = ['playlist_id', 'user_id']; // Atributos que pueden ser asignados en masa

    /**
     * Relación con el modelo Playlist.
     * Una playlist compartida pertenece a una playlist.
     */
    public function playlist()
    {
        return $this->belongsTo(Playlist::class, 'playlist_id');
    }

    /**
     * Relación con el modelo User.
     * Una playlist compartida pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
