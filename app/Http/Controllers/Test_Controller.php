<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Playlist;
use App\Models\Song;
use App\Models\PlaybackHistory;
use App\Models\PlaylistSong;
use App\Models\SharedPlaylist;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Follow;
use App\Models\Post;


class Test_Controller extends Controller
{
    public function add_user()
    {
        // Crear un nuevo usuario
        $user = new User();
        // Insertar uuid
        /*$user->id = '8f6eea1e-9011-40ec-8ae4-19916125272b';
        $user->username = 'John Doe';
        $user->birthday = '1990-01-01';
        $user->country = 'United States';
        $user->email = 'john.doe@gmail.com';
        $user->password = Hash::make('1234');
        $user->role = 'artist';
        $user->description = 'Hello, I am John Doe';
        $user->register_date = date('Y-m-d H:i:s');
        $user->statement_of_account = 0;
        */
        $user->id = '8f6eea1e-9011-40ec-8ae4-19916127672b';
        $user->username = 'Valentina';
        $user->birthday = '1995-01-01';
        $user->country = 'Colombia';
        $user->email = 'valentina@gmail.com';
        $user->password = Hash::make('1234');
        $user->role = 'user';
        $user->description = 'Hello, I am Valentina';
        $user->register_date = date('Y-m-d H:i:s');

        // Guardar el usuario en la base de datos
        $user->save();
        return User::all();
    }

    public function add_artist()
    {
        $artist = new Artist();
        $artist->user_id = '8f6eea1e-9011-40ec-8ae4-19916125272b'; // Usar un UUID existente de la tabla `users`
        $artist->verified = true;
        $artist->about = 'I am a passionate musician.';
        $artist->save();

        return Artist::all();
    }

    public function add_album()
    {
        $album = new Album();
        $album->title = 'Greatest Hits';
        $album->artist_id = 1; // Usar un ID existente de la tabla `artists`
        $album->release_date = '2024-09-01';
        $album->description = 'A collection of the greatest hits.';
        $album->icon = 'greatest_hits.png';
        $album->save();

        return Album::all();
    }

    public function add_song()
    {
        $song = new Song();
        $song->title = 'Hit Song';
        $song->artist_id = 1; // Usar un ID existente de la tabla `artists`
        $song->album_id = 1; // Usar un ID existente de la tabla `albums`
        $song->time = '03:30:00';
        $song->genre = 'Pop';
        $song->url_song = 'https://example.com/hit_song.mp3';
        $song->save();

        return Song::all();
    }

    public function add_post()
    {
        $post = new Post();
        $post->user_id = '8f6eea1e-9011-40ec-8ae4-19916125272b'; // Usar un UUID existente de la tabla `users`
        $post->content = 'Check out my new song!';
        $post->publishing_date = now()->toDateString();
        $post->image = 'post_image.png';
        $post->song_id = 2; // Usar un ID existente de la tabla `songs`
        $post->save();

        return Post::all();
    }

    public function add_like()
    {
        $like = new  Like();
        $like->user_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar un UUID existente de la tabla `users`
        $like->post_id = 1; // Usar un ID existente de la tabla `posts`
        $like->save();

        return  Like::all();
    }

    public function add_follow()
    {
        $follow = new Follow();
        $follow->follower_id = '8f6eea1e-9011-40ec-8ae4-19916125272b'; // Usar un UUID existente de la tabla `users`
        $follow->following_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar otro UUID existente de la tabla `users`
        $follow->follow_up_date = now()->toDateString();
        $follow->save();

        return Follow::all();
    }

    public function add_playlist()
    {
        $playlist = new Playlist();
        $playlist->name = 'My Favorite Songs';
        $playlist->user_id = '8f6eea1e-9011-40ec-8ae4-19916125272b'; // Usar un UUID existente de la tabla `users`
        $playlist->release_date = now()->toDateString();
        $playlist->description = 'A collection of my favorite songs.';
        $playlist->privacy = 'shared';
        $playlist->image = 'playlist_image.png';
        $playlist->save();

        return Playlist::all();
    }

    public function add_playlist_song()
    {
        $playlistSong = new  PlaylistSong();
        $playlistSong->playlist_id = 1; // Usar un ID existente de la tabla `playlists`
        $playlistSong->song_id = 2; // Usar un ID existente de la tabla `songs`
        $playlistSong->date_added = now()->toDateString();
        $playlistSong->save();

        return  PlaylistSong::all();
    }

    public function add_shared_playlist()
    {
        $sharedPlaylist = new  SharedPlaylist();
        $sharedPlaylist->playlist_id = 1; // Usar un ID existente de la tabla `playlists`
        $sharedPlaylist->user_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar un UUID existente de la tabla `users`
        $sharedPlaylist->save();

        return  SharedPlaylist::all();
    }

    public function add_playback_history()
    {
        $playbackHistory = new  PlaybackHistory();
        $playbackHistory->user_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar un UUID existente de la tabla `users`
        $playbackHistory->song_id = 2; // Usar un ID existente de la tabla `songs`
        $playbackHistory->play_date = now();
        $playbackHistory->save();

        return  PlaybackHistory::all();
    }

    public function add_comment()
    {
        $comment = new  Comment();
        $comment->user_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar un UUID existente de la tabla `users`
        $comment->post_id = 1; // Usar un ID existente de la tabla `posts`
        $comment->content = 'Great post!';
        $comment->publication_date = now();
        $comment->save();

        return Comment::all();
    }
}
