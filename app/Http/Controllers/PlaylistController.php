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
    public function index(Request $request)
    {
        try {
            //Obtener todas las listas de reproducción del usuario
            $playlists = Playlist::where('user_id', $request->user()->id)->get();
            $sharedPlaylists = SharedPlaylist::where('user_id', $request->user()->id)->get();
            $followedPlaylists = FollowedPlaylist::where('user_id', $request->user()->id)->get();

            // Obtener la información de las listas de reproducción compartidas
            $sharedPlaylistsInfo = [];

            foreach ($sharedPlaylists as $sharedPlaylist) {
                $playlist = Playlist::find($sharedPlaylist->playlist_id);
                $user = User::find($playlist->user_id);
                $sharedPlaylistsInfo[] = [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'image' => $playlist->image,
                    'owner' => $user->nameusername
                ];
            }

            // Obtener la información de las listas de reproducción seguidas
            $followedPlaylistsInfo = [];

            foreach ($followedPlaylists as $followedPlaylist) {
                $playlist = Playlist::find($followedPlaylist->playlist_id);
                $user = User::find($playlist->user_id);
                $followedPlaylistsInfo[] = [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'image' => $playlist->image,
                    'owner' => $user->nameusername
                ];
            }

            // Obtener la información de las listas de reproducción propias
            $playlistsInfo = [];

            foreach ($playlists as $playlist) {
                $user = User::find($playlist->user_id);
                $playlistsInfo[] = [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'image' => $playlist->image,
                    'owner' => $user->username
                ];
            }

            return response()->json([
                'playlists' => $playlistsInfo,
                'shared_playlists' => $sharedPlaylistsInfo,
                'followed_playlists' => $followedPlaylistsInfo
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error getting playlists'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Comprobar si el usuario existe
            $user = User::find($request->user()->id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            // Comprobar cuantas listas de reproducción tiene el usuario entre propias y compartidas y seguidas
            $playlists = Playlist::where('user_id', $request->user()->id)->get();
            $sharedPlaylists = SharedPlaylist::where('user_id', $request->user()->id)->get();
            $followedPlaylists = FollowedPlaylist::where('user_id', $request->user()->id)->get();

            $Nplaylists = count($playlists) + count($sharedPlaylists) + count($followedPlaylists);

            // Crear una nueva lista de reproducción
            $playlist = new Playlist();
            $playlist->name = "Playlist #" . ($Nplaylists + 1);
            $playlist->user_id = $request->user()->id;
            $playlist->save();

            return response()->json([
                'message' => 'Playlist created successfully',
                'playlist' => $playlist
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating playlist'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Obtener la lista de reproducción por id y nombre del propietario
            $playlist = Playlist::find($id);

            // Si la lista de reproducción no existe o is_active es false
            if (!$playlist) {
                return response()->json([
                    'message' => 'Playlist not found'
                ], 404);
            }

            if (!$playlist->is_active) {
                return response()->json([
                    'message' => 'Playlist not found'
                ], 404);
            }

            // Obtener el propietario de la lista de reproducción, username y profile_picture

            $user = User::find($playlist->user_id);

            $playlistInfo = [
                'id' => $playlist->id,
                'name' => $playlist->name,
                'image' => $playlist->image,
                'owner' => $user->username,
                'owner_image' => $user->profile_picture
            ];

            // si la lista de reproducción es compartida mostrar el propietario y las personas con las que se ha compartido
            $sharedPlaylist = SharedPlaylist::where('playlist_id', $playlist->id)->get();
            $sharedPlaylistInfo = [];

            if (count($sharedPlaylist) > 0) {
                foreach ($sharedPlaylist as $shared) {
                    $user = User::find($shared->user_id);
                    $sharedPlaylistInfo[] = [
                        'id' => $user->id,
                        'username' => $user->username,
                        'profile_picture' => $user->profile_picture
                    ];
                }
            }

            $playlistResponse = [
                'id' => $playlist['id'],
                'name' => $playlist['name'],
                'description' => $playlist['description'],
                'image' => $playlist['image'],
                'user_id' => $playlist['user_id'],
                'username' => $user->username,
                'profile_picture' => $user->profile_picture,
                'privacy' => $playlist['privacy'],
                'shared_with' => $sharedPlaylistInfo
            ];

            return response()->json([
                'playlist' => $playlistResponse
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error getting playlist'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Buscar la lista de reproducción por ID
            $playlist = Playlist::find($id);

            // Si la lista de reproducción no existe
            if (!$playlist) {
                return response()->json([
                    'message' => 'Playlist not found'
                ], 404);
            }

            // Comprobar si el usuario existe y es el propietario de la lista de reproducción 
            $user = User::find($request->user()->id);

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            if ($playlist->user_id !== $request->user()->id) {
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
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating playlist'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            // Buscar la lista de reproducción por ID
            $playlist = Playlist::find($id);

            // Si la lista de reproducción no existe
            if (!$playlist) {
                return response()->json([
                    'message' => 'Playlist not found'
                ], 404);
            }

            // Comprobar si el usuario existe y es el propietario de la lista de reproducción
            $user = User::find($request->user()->id);

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            if ($playlist->user_id !== $request->user()->id) {
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
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting playlist'], 500);
        }
    }
}
