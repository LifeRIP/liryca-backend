<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongRequest;
use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
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
            $songs = Song::all();
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
                'artist_id' => 'required|exists:artists,id',
                'album_id' => 'nullable|exists:albums,id',
                'time' => 'required|date_format:H:i:s', // Formato HH:MM:SS
                'genre' => 'required|string|max:100',
                'url_song' => 'required|url',
                'is_active' => 'boolean',
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Validar si la canción con el mismo título y artista ya existe
            $existingSong = Song::where('title', $request->title)
                ->where('artist_id', $request->artist_id)
                ->first();
            if ($existingSong) {
                return response()->json([
                    'success' => false,
                    'message' => 'Song title already exists'
                ], 400);
            }

            // Crear una nueva canción
            $song = Song::create($request->all());
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
}
