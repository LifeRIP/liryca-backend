<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongRequest;
use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use App\Models\SongCollaborator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            // Obtener todas las canciones con su informacion y el user_id y nombre de los colaboradores
            $songs = Song::all();
            foreach ($songs as $song) {
                $collaborators = SongCollaborator::where('song_id', $song->id)
                    ->join('artists', 'song_collaborators.artist_id', '=', 'artists.id')
                    ->join('users', 'artists.user_id', '=', 'users.id')
                    ->select('users.id', 'users.name')
                    ->get();
                $song->collaborators = $collaborators;
            }

            return response()->json([
                'success' => true,
                'data' => $songs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'album_id' => 'nullable|exists:albums,id',
                'time' => 'required|date_format:H:i:s', // Formato HH:MM:SS
                'genre' => 'required|string|max:100',
                'url_song' => 'required|url',
                'collaborators' => 'array', // Colaboradores
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            $artist = Artist::where('user_id', $request->user()->id)->first();

            // Validar si la canción con el mismo título y artista ya existe
            $existingSong = Song::where('title', $request->title)
                ->where('artist_id', $artist->id)
                ->first();
            if ($existingSong) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song title already exists'
                ], 400);
            }

            // Crear una nueva canción
            $song = Song::create([
                'title' => $request->title,
                'artist_id' => $artist->id,
                'album_id' => $request->album_id,
                'time' => $request->time,
                'genre' => $request->genre,
                'url_song' => $request->url_song,
            ]);

            // Crear los colaboradores de la canción
            if ($request->collaborators) {
                foreach ($request->collaborators as $collaborator) {
                    SongCollaborator::create([
                        'song_id' => $song->id,
                        'artist_id' => $collaborator
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $song
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            // Verificar si la canción existe
            $song = Song::find($id);
            if (!$song) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song not found'
                ], 404);
            }

            // Obtener los colaboradores de la canción
            $collaborators = SongCollaborator::where('song_id', $song->id)
                ->join('artists', 'song_collaborators.artist_id', '=', 'artists.id')
                ->join('users', 'artists.user_id', '=', 'users.id')
                ->select('users.id', 'users.name')
                ->get();
            $song->collaborators = $collaborators;

            // Mostrar la canción
            return response()->json([
                'success' => true,
                'data' => $song
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'title' => 'string|max:255',
                'artist_id' => 'exists:artists,id',
                'album_id' => 'nullable|exists:albums,id',
                'time' => 'date_format:H:i:s', // Formato HH:MM:SS
                'genre' => 'string|max:100',
                'url_song' => 'url',
                'is_active' => 'boolean',
                'collaborators' => 'array', // Colaboradores
            ]);

            // Verificar si la canción existe
            $song = Song::find($id);
            if (!$song) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song not found'
                ], 404);
            }

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Actualizar la canción
            $song->update($request->all());

            // Actualizar los colaboradores de la canción

            // Eliminar los colaboradores actuales
            SongCollaborator::where('song_id', $song->id)->delete();

            // Crear los nuevos colaboradores

            if ($request->collaborators) {
                foreach ($request->collaborators as $collaborator) {
                    SongCollaborator::create([
                        'song_id' => $song->id,
                        'artist_id' => $collaborator
                    ]);
                }
            }

            $song->collaborators = SongCollaborator::where('song_id', $song->id)
                ->join('artists', 'song_collaborators.artist_id', '=', 'artists.id')
                ->join('users', 'artists.user_id', '=', 'users.id')
                ->select('users.id', 'users.name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $song
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            // Verificar si la canción existe
            $song = Song::find($id);
            if (!$song) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song not found'
                ], 404);
            }

            // Eliminar la canción
            $song->delete();

            // Eliminar los colaboradores de la canción
            SongCollaborator::where('song_id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Song deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getTopSongsByArtist($artistId)
    {
        $topSongs = Song::where('artist_id', $artistId)
            ->where('is_active', true)
            ->withCount('playbackHistories as play_count')
            ->orderByDesc('play_count')
            ->take(10)
            ->get();

        if ($topSongs->isEmpty()) {
            return response()->json(['error' => 'No songs found for this artist'], 404);
        }

        return response()->json([
            'artist_id' => $artistId,
            'top_songs' => $topSongs,
        ]);
    }
}
