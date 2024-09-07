<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    public $incrementing = false; // No tiene un ID propio, ya que la clave es compuesta
    protected $primaryKey = ['follower_id', 'following_id']; // Clave primaria compuesta
    protected $fillable = ['follower_id', 'following_id', 'follow_up_date'];

    /**
     * Relación con el modelo User para el seguidor.
     * Un seguimiento pertenece a un usuario (seguidor).
     */
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * Relación con el modelo User para el seguido.
     * Un seguimiento pertenece a un usuario (seguido).
     */
    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
