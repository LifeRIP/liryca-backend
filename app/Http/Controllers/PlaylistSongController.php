<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use illuminate\Http\JsonResponse;
use App\Models\PlaylistSong;
use App\Models\Playlist;
use App\Models\SharedPlaylist;
use App\Models\Song;


class PlaylistSongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los datos de las canciones de las playlists
        $playlistSongs = PlaylistSong::all();

        // Obtener el nombre de la playlist y de la canción creando un json en el cual se verá el nombre de la playlist y el nombre de las canciones contenidas en ella
        $playlistSongs->map(function ($playlistSong) {
            $playlistSong->playlist = Playlist::find($playlistSong->playlist_id);
            $playlistSong->song = Song::find($playlistSong->song_id);
            return $playlistSong;
        });

        return response()->json([
            'success' => true,
            'data' => $playlistSongs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        // Verificar si la canción ya está en la playlist
        $existingPlaylistSong = PlaylistSong::where('playlist_id', $request->playlist_id)
            ->where('song_id', $request->song_id)
            ->first();

        if ($existingPlaylistSong) {
            return response()->json([
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
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request): JsonResponse
    {
        // Devolver la información de la playlist y la información de las canciones contenidas en ella
        $playlistSongs = PlaylistSong::where('playlist_id', $request->playlist_id)->get();

        // Si la playlist no existe, devolver un mensaje de error
        if ($playlistSongs->isEmpty()) {
            return response()->json([
                'message' => 'Playlist not found'
            ], 404);
        }

        // Obtener el nombre de la playlist y de la canción creando un json en el cual se verá el nombre de la playlist y el nombre de las canciones contenidas en ella

        $playlistSongs->map(function ($playlistSong) {
            $playlistSong->playlist = Playlist::find($playlistSong->playlist_id);
            $playlistSong->song = Song::find($playlistSong->song_id);
            return $playlistSong;
        });

        // Si la playlist no tiene canciones, devolver un mensaje de error

        if ($playlistSongs->isEmpty()) {
            return response()->json([
                'message' => 'Playlist has no songs'
            ], 404);
        }

        // Devolver la información de la playlist y la información de las canciones contenidas en ella

        return response()->json([
            'success' => true,
            'data' => $playlistSongs
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): JsonResponse
    {
        // Eliminar la canción de la playlist
        try {
            // Validar los datos de la petición
            $validator = Validator::make($request->all(), [
                'playlist_id' => 'required',
                'song_id' => 'required'
            ]);

            // Si la validación falla, devolver un mensaje de error
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Validar que exista la relación entre la playlist y la canción
            $playlistSong = PlaylistSong::where('playlist_id', $request->playlist_id)
                ->where('song_id', $request->song_id)
                ->first();

            // Si la relación no existe, devolver un mensaje de error
            if (!$playlistSong) {
                return response()->json([
                    'success' => false,
                    'message' => 'Playlist song not found'
                ], 404);
            }

            // Validar que la persona que está eliminando la canción de la playlist sea el dueño de la playlist o la persona que la añadió
            $playlist = Playlist::find($request->playlist_id);
            $sharedPlaylist = SharedPlaylist::where('playlist_id', $request->playlist_id)
                ->where('user_id', $request->user()->id)
                ->first();

            if ($playlist->user_id !== $request->user()->id && !$sharedPlaylist) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

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
}
