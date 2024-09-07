<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación con Artist
    public function artist()
    {
        return $this->hasOne(Artist::class, 'userID');
    }

    // Relación con Post
    public function posts()
    {
        return $this->hasMany(Post::class, 'userID');
    }

    // Relación con Like
    public function likes()
    {
        return $this->hasMany(Like::class, 'userID');
    }

    // Relación con Follow (seguidores y seguidos)
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followingID', 'followerID');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'followerID', 'followingID');
    }

    // Relación con Playlist
    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'userID');
    }

    // Relación con PlaybackHistory
    public function playbackHistory()
    {
        return $this->hasMany(PlaybackHistory::class, 'userID');
    }

    // Relación con Comment
    public function comments()
    {
        return $this->hasMany(Comment::class, 'userID');
    }
}
