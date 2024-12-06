<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Follow;
use App\Models\PlaybackHistory;
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

    public function show(string $user_id)
    {
        try {

            //Información del artista mediante el id
            $artist = DB::table('users')
                ->join('artists', 'users.id', '=', 'artists.user_id')
                ->select('users.username', 'artists.user_id', 'artists.id', 'users.birthday', 'users.country', 'users.email', 'users.description', 'users.is_active', 'users.profile_picture', 'users.profile_banner', 'artists.about')
                ->where('artists.user_id', $user_id)
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

    public function getFollowingArtists(Request $request)
    {
        try {
            // Obtener los IDs de los artistas que sigue
            $artistIds = Follow::where('follower_id', $request->user()->id)
                ->whereHas('following', function ($query) {
                    $query->where('role', 'artist'); // Solo artistas
                })
                ->pluck('following_id');

            // Obtener detalles de los artistas
            $artists = Artist::whereIn('user_id', $artistIds)
                ->take(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $artists->map(function ($artist) {
                    return [
                        'id' => $artist->id,
                        'user_id' => $artist->user_id,
                        'username' => $artist->user->username,
                        'profile_picture' => $artist->user->profile_picture
                    ];
                }),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred'
            ]);
        }
    }
    public function getTopArtistsToday()
    {
        try {
            //$today = now(tz: 'America/Bogota')->startOfDay();

            $topArtists = PlaybackHistory::with(['song.artist.user'])
                ->select('artists.id', 'artists.user_id', 'users.username', 'users.profile_picture', DB::raw('COUNT(*) as play_count'))
                ->join('songs', 'playback_histories.song_id', '=', 'songs.id')
                ->join('artists', 'songs.artist_id', '=', 'artists.id')
                ->join('users', 'artists.user_id', '=', 'users.id')
                //->where('playback_histories.created_at', '>=', $today)
                ->groupBy('artists.id', 'artists.user_id', 'users.username', 'users.profile_picture')
                ->orderByDesc('play_count')
                ->take(10)
                ->get();

            if ($topArtists->count() === 0) {
                return response()->json([
                    'success' => false,
                    'data' => 'No artist found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $topArtists->map(function ($artist) {
                    return [
                        'id' => $artist->id,
                        'user_id' => $artist->user_id,
                        'username' => $artist->user->username,
                        'profile_picture' => $artist->profile_picture
                    ];
                }),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred'
            ]);
        }
    }
}
