<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongCollaborator extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = ['song_id', 'artist_id'];
    protected $keyType = 'int';
    protected $fillable = ['song_id', 'artist_id'];

    /**
     * Relación con el modelo Song
     */
    public function song()
    {
        return $this->belongsTo(Song::class, 'song_id');
    }

    /**
     * Relación con el modelo Artist
     */
    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }
}
