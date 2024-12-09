<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use illuminate\Http\JsonResponse;
use App\Models\PlaylistSong;
use App\Models\Playlist;
use App\Models\SharedPlaylist;
use App\Models\FollowedPlaylist;
use App\Models\Song;
use App\Models\Album;
use App\Models\Artist;
use App\Models\User;


class PlaylistSongController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de la petición
            $validator = Validator::make($request->all(), [
                'playlist_id' => 'required',
                'song_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Verificar si la canción ya está en la playlist
            $playlistSong = PlaylistSong::where('playlist_id', $request->playlist_id)
                ->where('song_id', $request->song_id)
                ->first();

            if ($playlistSong) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song already in playlist'
                ], 400);
            }

            // Verificar si la persona que está añadiendo la canción a la playlist es el dueño de la playlist o un invitado
            // comprobar si el  $request->user()->id es igual al id del usuario que creo la playlist o si está en la tabla de shared_playlist
            $playlist = Playlist::find($request->playlist_id);
            $sharedPlaylist = SharedPlaylist::where('playlist_id', $request->playlist_id)
                ->where('user_id', $request->user()->id)
                ->first();

            if ($playlist->user_id !== $request->user()->id && !$sharedPlaylist) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Crear una nueva relación entre la playlist y la canción
            $playlistSong = new PlaylistSong();
            $playlistSong->playlist_id = $request->playlist_id;
            $playlistSong->song_id = $request->song_id;
            $playlistSong->add_by = $request->user()->id;
            $playlistSong->save();

            return response()->json([
                'success' => true,
                'data' => $playlistSong
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            // Verificar si la petición es realizada por el dueño de la playliss, un seguirdor o un invitado o si la playlist es privada
            $playlist = Playlist::find($id);

            if (!$playlist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Playlist not found'
                ], 404);
            }

            if ($playlist->user_id !== $request->user()->id) {
                $sharedPlaylist = SharedPlaylist::where('playlist_id', $id)
                    ->where('user_id', $request->user()->id)
                    ->first();

                $followedPlaylist = FollowedPlaylist::where('playlist_id', $id)
                    ->where('user_id', $request->user()->id)
                    ->first();

                if (!$sharedPlaylist && !$followedPlaylist && $playlist->is_private) {
                    return response()->json([
                        'message' => 'Unauthorized'
                    ], 401);
                }
            }

            // Obtener todas las canciones de la playlist
            $playlistSongs = PlaylistSong::where('playlist_id', $id)->get();

            // Añadir Username de el add_by y profile_picture de el usuario que añadió la canción dentro del campo add_by

            $playlistSongs->map(function ($playlistSong) {
                $playlistSong->add_by = User::select('id', 'username', 'profile_picture')
                    ->where('id', $playlistSong->add_by)
                    ->first();

                return $playlistSong;
            });

            // Obtener por medio de un select nombre de la canción, time, url_song, nombre del album, portada del album, username del artista de las canciones de la playlist
            $playlistSongs->map(function ($playlistSong)  use ($request) {

                $playlistSong->song = Song::select('title', 'time', 'url_song', 'album_id')
                    ->where('id', $playlistSong->song_id)
                    ->first();

                //comprobar si la canción tiene like
                $playlistSong->song->is_liked = Playlist::where('user_id', $request->user()->id)
                    ->where('name', 'LikedSongs')
                    ->first()
                    ->songs()
                    ->where('song_id', $playlistSong->song_id)
                    ->exists();


                $playlistSong->album = Album::select('title', 'icon', 'artist_id')
                    ->where('id', $playlistSong->song->album_id)
                    ->first();

                $playlistSong->artist = Artist::select('user_id')
                    ->where('id', $playlistSong->album->artist_id)
                    ->first();

                $playlistSong->artist->username = User::select('username')
                    ->where('id', $playlistSong->artist->user_id)
                    ->first()
                    ->username;

                return $playlistSong;
            });

            return response()->json([
                'success' => true,
                'data' => $playlistSongs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $data)
    {
        // separar el string data en playlistid y songid mediante &
        $data = explode('&', $data);
        $playlistid = $data[0];
        $songid = $data[1];

        //string $playlistid, string $songid
        try {
            //Validar datos de la petición 
            $validator = Validator::make(['playlist_id' => $playlistid, 'song_id' => $songid], [
                'playlist_id' => 'required',
                'song_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Validar que exista la relación entre la playlist y la canción
            $playlistSong = PlaylistSong::where('playlist_id', $playlistid)
                ->where('song_id', $songid)
                ->first();

            if (!$playlistSong) {
                return response()->json([
                    'success' => false,
                    'message' => 'Playlist song not found'
                ], 404);
            }

            // Validar que la persona que está eliminando la canción de la playlist sea el dueño de la playlist o la persona que la añadió
            $playlist = Playlist::find($playlistid);

            $sharedPlaylist = SharedPlaylist::where('playlist_id', $playlistid)
                ->where('user_id', $request->user()->id)
                ->first();

            if ($playlistSong->add_by !== $request->user()->id && $playlist->user_id !== $request->user()->id && !$sharedPlaylist) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            $playlistSong = PlaylistSong::where('playlist_id', $playlistid)
                ->where('song_id', $songid);

            // Eliminar la relación
            $playlistSong->delete();

            return response()->json([
                'success' => true,
                'message' => 'Playlist song deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listOfPlaylist(Request $request, string $id)
    {
        try {

            //verificar la existencia de la cancion
            $song = Song::find($id);

            if (!$song) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song not found'
                ], 404);
            }

            //obtener el id de todas mis playlist y de las playlist que me han compartido
            $myPlaylists = Playlist::where('user_id', $request->user()->id)
                ->get();

            $sharedPlaylists = SharedPlaylist::select('playlist_id')
                ->where('user_id', $request->user()->id)
                ->get();

            //obtener la informacion de las playlist con los playlist_id obtenidos
            $playlists = Playlist::whereIn('id', $myPlaylists->pluck('id'))
                ->orWhereIn('id', $sharedPlaylists->pluck('playlist_id'))
                ->get();

            // buscar si la cancion ya esta en alguna de mis playlist
            $playlists->map(function ($playlist) use ($id) {
                $playlistSong = PlaylistSong::where('playlist_id', $playlist->id)
                    ->where('song_id', $id)
                    ->first();

                if ($playlistSong) {
                    $playlist->song_exists = true;
                } else {
                    $playlist->song_exists = false;
                }

                return $playlist;
            });

            //Retornar solo id, nombre, image, song_exists de las playlist
            $playlists = $playlists->map(function ($playlist) {
                return [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'image' => $playlist->image,
                    'song_exists' => $playlist->song_exists
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $playlists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function likeSong(Request $request, string $id)
    {
        try {
            //verificar la existencia de la cancion
            $song = Song::find($id);

            if (!$song) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song not found'
                ], 404);
            }

            // Comprobar si la playlist LikedSong existe sino crearla
            $playlist = Playlist::where('user_id', $request->user()->id)
                ->where('name', 'LikedSongs')
                ->first();

            if (!$playlist) {
                $playlist = new Playlist();
                $playlist->user_id = $request->user()->id;
                $playlist->name = 'LikedSongs';
                $playlist->privacy = 'private';
                $playlist->image = 'like.png';
                $playlist->save();
            }

            // Verificar si la canción ya está en la playlist LikedSongs
            $playlistSong = PlaylistSong::where('playlist_id', $playlist->id)
                ->where('song_id', $id)
                ->first();

            if ($playlistSong) {
                //Eliminar la relación
                $playlistSong = PlaylistSong::where('playlist_id', $playlist->id)
                    ->where('song_id', $id);

                $playlistSong->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Song removed from LikedSongs'
                ]);
            }

            // Crear una nueva relación entre la playlist LikedSongs y la canción
            $playlistSong = new PlaylistSong();
            $playlistSong->playlist_id = $playlist->id;
            $playlistSong->song_id = $id;
            $playlistSong->add_by = $request->user()->id;
            $playlistSong->save();

            return response()->json([
                'success' => true,
                'message' => 'Song added to LikedSongs'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
