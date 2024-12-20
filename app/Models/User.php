<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasUuids, HasApiTokens, CanResetPassword, HasRoles;

    public $incrementing = false; // No usar ID incremental
    protected $keyType = 'string'; // Tipo de la clave primaria como string

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'is_migrated',
        'birthday',
        'country',
        'email',
        'password',
        'external_id',
        'external_auth',
        'two_factor_secret',
        'enable_two_factor_auth',
        'role',
        'description',
        'is_active',
        'profile_picture',
        'profile_banner',
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
            'is_active' => 'boolean',
            'birthday' => 'date',
            'two_factor_secret' => 'encrypted',
        ];
    }

    // Relación con Artist
    public function artist()
    {
        return $this->hasOne(Artist::class, 'user_id');
    }

    // Relación con Post
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    // Relación con los likes de los posts
    public function postlikes()
    {
        return $this->hasMany(PostLike::class, 'user_id');
    }

    // Relación con los likes de los comentarios
    public function commentLikes()
    {
        return $this->hasMany(CommentLike::class);
    }

    // Relación con Follow (seguidores y seguidos)
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }

    // Relación con Playlist
    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'user_id');
    }

    // Relación con playlistSong
    public function playlistSongs()
    {
        return $this->hasMany(PlaylistSong::class, 'add_by');
    }

    // Relación con PlaybackHistory
    public function playbackHistory()
    {
        return $this->hasMany(PlaybackHistory::class, 'user_id');
    }

    // Relación con Comment
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    // Relación con FollowAlbum
    public function followAlbums()
    {
        return $this->hasMany(FollowAlbum::class, 'user_id');
    }

    // Relación con FollowedPlaylist
    public function followedPlaylists()
    {
        return $this->hasMany(FollowedPlaylist::class, 'user_id');
    }
}
