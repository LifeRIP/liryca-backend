<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\PlaybackHistory;


class PlaybackHistoryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            //Validar los datos
            $validator = Validator::make($request->all(), [
                'song_id' => 'required|exists:songs,id'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            //Crear el nuevo historial de reproduccion
            $playbackHistory = new PlaybackHistory();
            $playbackHistory->user_id = $request->user()->id;
            $playbackHistory->song_id = $request->song_id;
            $playbackHistory->save();

            return response()->json($playbackHistory, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al guardar el historial de reproduccion'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function index(Request $request)
    {
        try {
            //Obtener el historial de reproduccion del usuario autenticado 

            $playbackHistory = PlaybackHistory::where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->get()->take(10);

            // si el song_id n+1 es igual al song_id n, eliminar el song_id n+1

            $playbackHistory = $playbackHistory->filter(function ($history, $key) use ($playbackHistory) {
                if ($key + 1 < $playbackHistory->count()) {
                    return $history->song_id != $playbackHistory[$key + 1]->song_id;
                }
                return true;
            });

            dd("flag");

            //comprobar si la canción tiene like
            $playbackHistory = $playbackHistory->map(function ($history) use ($request) {
                $history->is_liked = $request->user()->playlists()->where('name', 'LikedSongs')->first()->songs->contains($history->song_id);
                return $history;
            });

            //Agregar nombre de la cancion , foto del album, id de la cancion, url de la cancion, nombre del artista
            $playbackHistory = $playbackHistory->map(function ($history) {
                return [
                    'song_name' => $history->song->title,
                    "album_id" => $history->song->album->id,
                    'album_image' => $history->song->album->icon,
                    'song_id' => $history->song->id,
                    'song_url' => $history->song->url_song,
                    'artist_name' => $history->song->album->artist->user->username,
                    'artist_id' => $history->song->album->artist->user->id,
                    'is_liked' => $history->is_liked

                ];
            });

            dd("flag");

            return response()->json($playbackHistory, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener el historial de reproduccion'], 500);
        }
    }
}
