<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\FollowedPlaylist;

class FollowedPlaylistController extends Controller
{
    public function followPlaylist(Request $request, string $playlist_id)
    {
        try {
            //Validar los datos

            $validator = Validator::make(['playlist_id' => $playlist_id], [
                'playlist_id' => 'required|exists:playlists,id'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // Verificar si el usuario ya sigue la playlist
            $followedPlaylist = FollowedPlaylist::where('user_id', $request->user()->id)
                ->where('playlist_id', $playlist_id)
                ->first();

            if ($followedPlaylist) {
                // Si ya sigue la playlist dejar de seguirla

                $followedPlaylist->delete();

                return response()->json(['message' => 'Playlist dejada de seguir'], 200);
            }

            // Seguir la playlist
            $followedPlaylist = new FollowedPlaylist();
            $followedPlaylist->user_id = $request->user()->id;
            $followedPlaylist->playlist_id = $playlist_id;
            $followedPlaylist->save();

            return response()->json(['message' => 'Playlist seguida'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al seguir la playlist'], 500);
        }
    }
}
