<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\CommentLike;


class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Obtener todos mis comentarios en orden descendente
            $comments = Comment::where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Obtener los likes de los comentarios
            $comments = $comments->map(function ($comment) {
                $like = CommentLike::where('comment_id', $comment->id)
                    ->selectRaw('count(*) as likes')
                    ->first();

                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'post_id' => $comment->post_id,
                    'likes' => $like ? $like->likes : 0,
                    'comment_at' => $comment->created_at,
                ];
            });

            return response()->json($comments, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los comentarios'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            //Validar los datos

            $validator = Validator::make($request->all(), [
                'post_id' => 'required|exists:posts,id',
                'content' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // Guardar el comentario
            $comment = new Comment();
            $comment->user_id = $request->user()->id;
            $comment->post_id = $request->post_id;
            $comment->content = $request->content;
            $comment->save();

            return response()->json(['message' => 'Comentario guardado'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al guardar el comentario'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Buscar el comentario
            $comment = Comment::find($id);

            if (!$comment) {
                return response()->json(['message' => 'Comentario no encontrado'], 404);
            }

            // Verificar si el usuario es el dueño del comentario
            if ($comment->user_id != $request->user()->id) {
                return response()->json(['message' => 'No tienes permiso para editar el comentario'], 403);
            }

            //Validar los datos
            $validator = Validator::make($request->all(), [
                'content' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // Actualizar el comentario
            $comment->content = $request->content;
            $comment->save();

            return response()->json(['message' => 'Comentario actualizado'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el comentario'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            // Buscar el comentario
            $comment = Comment::find($id);

            if (!$comment) {
                return response()->json(['message' => 'Comentario no encontrado'], 404);
            }

            // Verificar si el usuario es el dueño del comentario
            if ($comment->user_id != $request->user()->id) {
                return response()->json(['message' => 'No tienes permiso para eliminar el comentario'], 403);
            }

            // Eliminar el comentario
            $comment->delete();

            return response()->json(['message' => 'Comentario eliminado'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el comentario'], 500);
        }
    }

    public function postComments(Request $request, string $post_id)
    {
        try {
            // Obtener los comentarios del post
            $post = Post::find($post_id);

            if (!$post) {
                return response()->json(['message' => 'Post no encontrado'], 404);
            }

            $comments = Comment::where('post_id', $post_id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Obtener los usuarios de los comentarios y los likes con map
            $comments = $comments->map(function ($comment)  use ($request) {
                $user = User::find($comment->user_id);

                $like = CommentLike::where('comment_id', $comment->id)
                    ->selectRaw('count(*) as likes')
                    ->first();

                // Validar si el usuario logueado le dio like al comentario
                $like->liked = CommentLike::where('comment_id', $comment->id)
                    ->where('user_id', $request->user()->id)
                    ->exists();

                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'likes' => $like ? $like->likes : 0,
                    'user_id' => $user->id,
                    'name' => $user->username,
                    'profile_picture' => $user->profile_picture,
                    'comment_at' => $comment->created_at,
                    'liked' => $like->liked

                ];
            });

            return response()->json($comments, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los comentarios del post'], 500);
        }
    }
}
