<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;

class ArtistController extends Controller
{
    // Index method devuelve la información del artista y su información de usuario
    public function index(Request $request)
    {
        try {
            // Verificar si el usuario es un artista con el rol de artista en users
            if ($request->user()->role !== 'artist') {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Obtener la información del usuario username, birthday, country, email, description, is_active, profile_picture, profile_banner y about de la tabla artist
            $artist = DB::table('users')
                ->join('artists', 'users.id', '=', 'artists.user_id')
                ->select('users.username', 'users.birthday', 'users.country', 'users.email', 'users.description', 'users.is_active', 'users.profile_picture', 'users.profile_banner', 'artists.about')
                ->where('users.id', $request->user()->id)
                ->first();

            // Si no hay artista
            if (!$artist) {
                return response()->json([
                    'message' => 'Artist not found'
                ], 404);
            }

            return response()->json($artist);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show(string $artist_id)
    {
        try {

            //Información del artista mediante el id
            $artist = DB::table('users')
                ->join('artists', 'users.id', '=', 'artists.user_id')
                ->select('users.username', 'users.birthday', 'users.country', 'users.email', 'users.description', 'users.is_active', 'users.profile_picture', 'users.profile_banner', 'artists.about')
                ->where('artists.id', $artist_id)
                ->first();

            return response()->json($artist);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request): JsonResponse
    {
        try {

            // Verificar si el usuario es un artista con el rol de artista en users
            if ($request->user()->role !== 'artist') {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }
            // Actualizar la información del artista en la tabla users
            $user = User::find($request->user()->id);

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $user->update([
                'username' => $request->username,
                'birthday' => $request->birthday,
                'country' => $request->country,
                'email' => $request->email,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'profile_picture' => $request->profile_picture,
                'profile_banner' => $request->profile_banner,
            ]);

            // Actualizar la información del artista en la tabla artists
            $artist = Artist::where('user_id', $request->user()->id)->first();

            if (!$artist) {
                return response()->json([
                    'message' => 'Artist not found'
                ], 404);
            }

            $artist->update([
                'about' => $request->about,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Artist updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
