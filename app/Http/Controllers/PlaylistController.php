<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Playlist;
use App\Models\User;
use App\Models\SharedPlaylist;
use App\Models\FollowedPlaylist;

class PlaylistController extends Controller
{
    // añadir una nueva lista de reproducción
    public function add_playlist(Request $request)
    {

        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'user_id' => 'required',
                'release_date' => 'required',
                'description' => 'required',
                'privacy' => 'required',
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

            //Crear una nueva lista de reproducción
            $playlist = new Playlist();
            $playlist->name = $request->name;
            $playlist->user_id = $request->user_id;
            $playlist->release_date = $request->release_date;
            $playlist->description = $request->description;
            $playlist->privacy = $request->privacy;
            $playlist->image = $request->image;
            $playlist->is_active = $request->is_active;
            $playlist->save();


            return response()->json([
                'message' => 'Playlist created successfully',
                'playlist' => $playlist
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener todas las listas de reproducción del usuario

    public function get_playlists(Request $request)
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Obtener el ID del usuario
            $user_id = $request->user_id;

            // Comprobar si el usuario existe
            $user = User::find($user_id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            // Obtener todas las listas de reproducción del usuario
            $playlists = Playlist::whereIn('id', function ($query) use ($user_id) {
                $query->select('p.id')
                    ->from('playlists as p')
                    ->leftjoin('shared_playlists as sp', 'p.id', '=', 'sp.playlist_id')
                    ->leftjoin('followed_playlists as fp', 'p.id', '=', 'fp.playlist_id')
                    ->where('p.user_id', $user_id)
                    ->orWhere('sp.user_id', $user_id)
                    ->orWhere('fp.user_id', $user_id);
            })->get();

            return response()->json([
                'message' => 'Playlists retrieved successfully',
                'playlists' => $playlists
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Modificar una lista de reproducción
    public function update_playlist(Request $request)
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'playlistID' => 'required',
                'user_id' => 'required',
                'name' => 'required',
                'release_date' => 'required',
                'description' => 'required',
                'privacy' => 'required',
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

            // Comprobar si la lista de reproducción existe
            $playlist = Playlist::find($request->playlistID);
            if (!$playlist) {
                return response()->json([
                    'message' => 'Playlist not found'
                ], 404);
            }

            // Comprobar si el usuario es el propietario de la lista de reproducción
            if ($playlist->user_id !== $request->user_id) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Actualizar los campos de la lista de reproducción
            if ($request->name) {
                $playlist->name = $request->name;
            }
            if ($request->release_date) {
                $playlist->release_date = $request->release_date;
            }
            if ($request->description) {
                $playlist->description = $request->description;
            }
            if ($request->privacy) {
                $playlist->privacy = $request->privacy;
            }
            if ($request->image) {
                $playlist->image = $request->image;
            }
            if ($request->is_active) {
                $playlist->is_active = $request->is_active;
            }
            $playlist->save();

            // Retornar un mensaje de éxito y la lista de reproducción actualizada
            return response()->json([
                'message' => 'Playlist updated successfully',
                'playlist' => $playlist
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminar una lista de reproducción
    public function delete_playlist(Request $request)
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'playlistID' => 'required',
                'user_id' => 'required'
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Comprobar si la lista de reproducción existe y está activa
            $playlist = Playlist::where('id', $request->playlistID)
                ->where('is_active', 1)
                ->first();

            if (!$playlist) {
                return response()->json([
                    'message' => 'Playlist not found'
                ], 404);
            }

            // Comprobar si el usuario es el propietario de la lista de reproducción
            if ($playlist->user_id !== $request->user_id) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Eliminar la lista de reproducción
            $playlist->is_active = 0;

            // Guardar los cambios
            $playlist->save();

            // Retornar un mensaje de éxito
            return response()->json([
                'message' => 'Playlist deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
