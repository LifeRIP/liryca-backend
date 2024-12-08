<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $posts = Post::all();
            return response()->json([
                'success' => true,
                'data' => $posts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'content' => 'required|string',
                'song_id' => 'exists:songs,id',
                'album_id' => 'exists:albums,id',
                'artist_id' => 'exists:artists,id',
                'playlist_id' => 'exists:playlists,id',
                'action_type' => 'in:shared,recommended,not_recommended',
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Crear un nuevo post con el user_id del usuario autenticado
            $post = new Post();
            $post->user_id = $request->user()->id;
            $post->content = $request->get('content');
            $post->song_id = $request->get('song_id');
            $post->album_id = $request->get('album_id');
            $post->artist_id = $request->get('artist_id');
            $post->playlist_id = $request->get('playlist_id');
            $post->action_type = $request->get('action_type');
            $post->save();

            // devolver una respuesta de el post se ha creado correctamente
            return response()->json($post, 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            // Verificar si el post existe
            $post = Post::find($id);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            // Mostrar el post
            return response()->json([
                'success' => true,
                'data' => $post
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'user_id' => 'exists:users,id',
                'content' => 'string',
                'song_id' => 'exists:songs,id',
                'album_id' => 'exists:albums,id',
                'artist_id' => 'exists:artists,id',
                'playlist_id' => 'exists:playlists,id',
                'action_type' => 'in:shared,recommended,not_recommended',
                'is_active' => 'boolean',
            ]);

            // Verificar si el post existe
            $post = Post::find($id);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Actualizar el post
            $post->update($request->all());
            return response()->json([
                'success' => true,
                'data' => $post
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            // Verificar si el post existe
            $post = Post::find($id);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            // Eliminar el post
            $post->delete();
            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
