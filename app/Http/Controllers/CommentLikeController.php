<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CommentLike;

class CommentLikeController extends Controller
{
    public function like(Request $request, string $comment_id)
    {
        try {
            //Validar los datos

            $validator = Validator::make(['comment_id' => $comment_id], [
                'comment_id' => 'required|exists:comments,id'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // Verificar si el usuario ya dio like al comentario

            $likedComment = CommentLike::where('user_id', $request->user()->id)
                ->where('comment_id', $comment_id)
                ->first();

            if ($likedComment) {
                // Si ya dio like al comentario quitar el like

                $likedComment->delete();

                return response()->json(['message' => 'Like quitado'], 200);
            }

            // Dar like al comentario
            $likedComment = new CommentLike();
            $likedComment->user_id = $request->user()->id;
            $likedComment->comment_id = $comment_id;
            $likedComment->save();

            return response()->json(['message' => 'Like dado'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al dar like al comentario'], 500);
        }
    }
}
