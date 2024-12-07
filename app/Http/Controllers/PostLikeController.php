<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PostLike;

class PostLikeController extends Controller
{
    public function like(Request $request, string $post_id)
    {
        try {
            //Validar los datos

            $validator = Validator::make(['post_id' => $post_id], [
                'post_id' => 'required|exists:posts,id'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // Verificar si el usuario ya dio like al post

            $likedPost = PostLike::where('user_id', $request->user()->id)
                ->where('post_id', $post_id)
                ->first();

            if ($likedPost) {
                // Si ya dio like al post quitar el like

                $likedPost->delete();

                return response()->json(['message' => 'Like quitado'], 200);
            }

            // Dar like al post
            $likedPost = new PostLike();
            $likedPost->user_id = $request->user()->id;
            $likedPost->post_id = $post_id;
            $likedPost->save();

            return response()->json(['message' => 'Like dado'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al dar like al post'], 500);
        }
    }
}
