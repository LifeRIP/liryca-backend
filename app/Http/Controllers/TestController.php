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
use App\Models\FollowedPlaylist;

class TestController extends Controller
{
    // Método para probar la creación de registros en la base de datos
    public function test()
    {
        // Crear un nuevo usuario
        $user = new User();
        // Insertar usuario artista
        $user->id = '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3';
        $user->username = 'John Doe';
        $user->birthday = '1990-01-01';
        $user->country = 'United States';
        $user->email = 'john.doe@gmail.com';
        $user->password = Hash::make('12345678');
        $user->role = 'artist';
        $user->description = 'Hello, I am John Doe';
        $user->is_active = 0;
        $user->save();

        // Insertar usuario normal
        $user = new User();
        $user->id = '8f6eea1e-9011-40ec-8ae4-19916127672b';
        $user->username = 'Valentina';
        $user->birthday = '1995-01-01';
        $user->country = 'Colombia';
        $user->email = 'valentina@gmail.com';
        $user->password = Hash::make('1234');
        $user->role = 'user';
        $user->description = 'Hello, I am Valentina';
        $user->save();

        // Crear un nuevo artista
        $artist = new Artist();
        $artist->user_id = '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3'; // Usar un UUID existente de la tabla `users`
        $artist->verified = true;
        $artist->about = 'I am a passionate musician.';
        $artist->save();

        // Crear un nuevo álbum
        $album = new Album();
        $album->title = 'Greatest Hits';
        $album->artist_id = $artist->id; // Usar un ID existente de la tabla `artists`
        $album->release_date = '2024-09-01';
        $album->description = 'A collection of the greatest hits.';
        $album->icon = 'greatest_hits.png';
        $album->save();

        // Crear una nueva canción
        $song = new Song();
        $song->title = 'Hit Song';
        $song->artist_id = $artist->id; // Usar un ID existente de la tabla `artists`
        $song->album_id = $album->id; // Usar un ID existente de la tabla `albums`
        $song->time = '00:03:30';
        $song->genre = 'Pop';
        $song->url_song = 'https://example.com/hit_song.mp3';
        $song->save();

        // Crear una nueva publicación
        $post = new Post();
        $post->user_id = '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3'; // Usar un UUID existente de la tabla `users`
        $post->content = 'Check out my new song!';
        $post->image = 'post_image.png';
        $post->song_id = $song->id; // Usar un ID existente de la tabla `songs`
        $post->save();

        // Crear un nuevo like
        $like = new Like();
        $like->user_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar un UUID existente de la tabla `users`
        $like->post_id = $post->id; // Usar un ID existente de la tabla `posts`
        $like->save();

        // Crear un nuevo follow
        $follow = new Follow();
        $follow->follower_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar un UUID existente de la tabla `users`
        $follow->followed_id = '264e1f6c-52ec-48ea-bfb1-13100f8b5cf3'; // Usar otro UUID existente de la tabla `users`
        $follow->save();

        // Crear una nueva lista de reproducción
        $playlist = new Playlist();
        $playlist->name = 'My Favorite Songs';
        $playlist->user_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar un UUID existente de la tabla `users`
        $playlist->description = 'A collection of my favorite songs.';
        $playlist->privacy = 'shared';
        $playlist->image = 'playlist_image.png';
        $playlist->save();

        // Crear una nueva canción en una lista de reproducción
        $playlistSong = new PlaylistSong();
        $playlistSong->playlist_id = $playlist->id; // Usar un ID existente de la tabla `playlists`
        $playlistSong->song_id = $song->id; // Usar un ID existente de la tabla `songs`
        $playlistSong->save();

        // Crear una lista de reproducción compartida
        $sharedPlaylist = new SharedPlaylist();
        $sharedPlaylist->playlist_id = $playlist->id; // Usar un ID existente de la tabla `playlists`
        $sharedPlaylist->user_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar un UUID existente de la tabla `users`
        $sharedPlaylist->save();

        // Crear un nuevo historial de reproducción
        $playbackHistory = new PlaybackHistory();
        $playbackHistory->user_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar un UUID existente de la tabla `users`
        $playbackHistory->song_id = $song->id; // Usar un ID existente de la tabla `songs`
        $playbackHistory->save();

        // Crear un nuevo comentario
        $comment = new Comment();
        $comment->user_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar un UUID existente de la tabla `users`
        $comment->post_id = $post->id; // Usar un ID existente de la tabla `posts`
        $comment->content = 'Great post!';
        $comment->save();

        // Crear un nuevo seguidor a una lista
        $followedPlaylist = new FollowedPlaylist();
        $followedPlaylist->user_id = '8f6eea1e-9011-40ec-8ae4-19916127672b'; // Usar un UUID existente de la tabla `users`
        $followedPlaylist->playlist_id = $playlist->id; // Usar un ID existente de la tabla `playlists`
        $followedPlaylist->save();

        return response()->json([
            'message' => 'Test data created successfully',
            'user' => $user,
            'artist' => $artist,
            'album' => $album,
            'song' => $song,
            'post' => $post,
            'like' => $like,
            'follow' => $follow,
            'playlist' => $playlist,
            'playlistSong' => $playlistSong,
            'sharedPlaylist' => $sharedPlaylist,
            'playbackHistory' => $playbackHistory,
            'comment' => $comment,
            'followedPlaylist' => $followedPlaylist
        ], 201);
    }
}
