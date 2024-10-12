<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Playlist;
use App\Models\User;
use App\Models\SharedPlaylist;
use App\Models\FollowedPlaylist;


class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todas las listas de reproducción publicas
        $playlists = Playlist::where('privacy', 'public')->get();

        //si no hay listas de reproducción publicas

        if (!$playlists) {
            return response()->json([
                'message' => 'No playlists found'
            ], 404);
        }

        return response()->json($playlists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los campos requeridos
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_id' => 'required',
            'description' => 'required',
            'privacy' => 'required' | 'in:public,private',
            'image' => 'required',
            'is_active' => 'required'
        ]);

        // Comprobar si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        // Comprobar si el usuario existe
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Crear una nueva lista de reproducción
        $playlist = new Playlist();
        $playlist->name = $request->name;
        $playlist->user_id = $request->user_id;
        $playlist->description = $request->description;
        $playlist->privacy = $request->privacy;
        $playlist->image = $request->image;
        $playlist->is_active = $request->is_active;
        $playlist->save();

        return response()->json([
            'message' => 'Playlist created successfully',
            'playlist' => $playlist
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Obtener la lista de reproducción por nombre
        $playlist = Playlist::where('name', $id)->first();
        if (!$playlist) {
            return response()->json([
                'message' => 'Playlist not found'
            ], 404);
        }
        return response()->json($playlist);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Buscar la lista de reproducción por ID
        $playlist = Playlist::find($id);

        // Si la lista de reproducción no existe
        if (!$playlist) {
            return response()->json([
                'message' => 'Playlist not found'
            ], 404);
        }

        // Comprobar si el usuario existe y es el propietario de la lista de reproducción
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if ($playlist->user_id !== $request->user_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // Actualizar la lista de reproducción
        $playlist->update($request->all());

        return response()->json([
            'message' => 'Playlist updated successfully',
            'playlist' => $playlist
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Buscar la lista de reproducción por ID
        $playlist = Playlist::find($id);

        // Si la lista de reproducción no existe
        if (!$playlist) {
            return response()->json([
                'message' => 'Playlist not found'
            ], 404);
        }

        // Comprobar si el usuario existe y es el propietario de la lista de reproducción
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if ($playlist->user_id !== $request->user_id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // Eliminar la lista de reproducción
        $playlist->delete();

        return response()->json([
            'message' => 'Playlist deleted successfully',
            'data' => $playlist
        ]);
    }
}
