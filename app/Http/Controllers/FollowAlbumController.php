<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\FollowAlbum;

class FollowAlbumController extends Controller
{
    public function followAlbum(Request $request, string $album_id)
    {
        try {
            //Validar los datos

            $validator = Validator::make(['album_id' => $album_id], [
                'album_id' => 'required|exists:albums,id'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // Verificar si el usuario ya sigue el album

            $followedAlbum = FollowAlbum::where('user_id', $request->user()->id)
                ->where('album_id', $album_id)
                ->first();

            if ($followedAlbum) {
                // Si ya sigue el album dejar de seguirlo

                $followedAlbum = FollowAlbum::where('user_id', $request->user()->id)
                    ->where('album_id', $album_id)
                    ->first();

                $followedAlbum->delete();

                return response()->json(['message' => 'Album dejado de seguir'], 200);
            }

            // Seguir el album
            $followedAlbum = new FollowAlbum();
            $followedAlbum->user_id = $request->user()->id;
            $followedAlbum->album_id = $album_id;
            $followedAlbum->save();

            return response()->json(['message' => 'Album seguido'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al seguir el album'], 500);
        }
    }
}
