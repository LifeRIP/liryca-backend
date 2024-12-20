<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\Song;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Playlist;
use Illuminate\Http\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Obtener todos los posts en orden
            $posts = Post::orderBy('created_at', 'desc')->get();


            // Ootener el tipo de post 
            foreach ($posts as $post) {
                switch (true) {
                    case $post->song_id:
                        $post->type = 'song';
                        break;
                    case $post->album_id:
                        $post->type = 'album';
                        break;
                    case $post->artist_id:
                        $post->type = 'artist';
                        break;
                    case $post->playlist_id:
                        $post->type = 'playlist';
                        break;
                    default:
                        $post->type = 'text';
                        break;
                }
            }

            // Organizar la información que se va a devolver
            $posts = $posts->map(function ($post) {
                switch ($post->type) {
                    case 'song':
                        return [
                            'id' => $post->id,
                            'user_id' => $post->user_id,
                            'profile_picture' => $post->user->profile_picture,
                            'username' => $post->user->username,
                            'action_type' => $post->action_type,
                            'content' => $post->content,
                            'type' => $post->type,
                            'song_id' => $post->song_id,
                            'title' => $post->song->title,
                            'duration' => $post->song->time,
                            'genre' => $post->song->genre,
                            'url_song' => $post->song->url_song,
                            'album_id' => $post->song->album->id,
                            'album_title' => $post->song->album->title,
                            'icon' => $post->song->album->icon,
                            'artist_id' => $post->song->artist->user_id,
                            'artist_name' => $post->song->artist->user->username,
                            'is_liked' => PostLike::where('post_id', $post->id)->where('user_id', request()->user()->id)->exists(),
                            'like_count' => PostLike::where('post_id', $post->id)->count(),
                            'comment_count' => Comment::where('post_id', $post->id)->count(),
                            'released_at' => $post->created_at,
                        ];
                    case 'album':
                        return [
                            'id' => $post->id,
                            'user_id' => $post->user_id,
                            'profile_picture' => $post->user->profile_picture,
                            'username' => $post->user->username,
                            'action_type' => $post->action_type,
                            'content' => $post->content,
                            'type' => $post->type,
                            'album_id' => $post->album_id,
                            'title' => $post->album->title,
                            'description' => $post->album->description,
                            'icon' => $post->album->icon,
                            'artist_id' => $post->album->artist->user_id,
                            'artist_name' => $post->album->artist->user->username,
                            'is_liked' => PostLike::where('post_id', $post->id)->where('user_id', request()->user()->id)->exists(),
                            'like_count' => PostLike::where('post_id', $post->id)->count(),
                            'comment_count' => Comment::where('post_id', $post->id)->count(),
                            'released_at' => $post->created_at,
                        ];
                    case 'artist':
                        return [
                            'id' => $post->id,
                            'user_id' => $post->user_id,
                            'profile_picture' => $post->user->profile_picture,
                            'username' => $post->user->username,
                            'action_type' => $post->action_type,
                            'content' => $post->content,
                            'type' => $post->type,
                            'artist_id' => $post->artist->user_id,
                            'name' => $post->artist->user->username,
                            'about' => $post->artist->about,
                            'artist_profile_picture' => $post->artist->user->profile_picture,
                            'artist_profile_banner' => $post->artist->user->profile_banner,
                            'is_liked' => PostLike::where('post_id', $post->id)->where('user_id', request()->user()->id)->exists(),
                            'like_count' => PostLike::where('post_id', $post->id)->count(),
                            'comment_count' => Comment::where('post_id', $post->id)->count(),
                            'released_at' => $post->created_at,
                        ];
                    case 'playlist':
                        return [
                            'id' => $post->id,
                            'user_id' => $post->user_id,
                            'profile_picture' => $post->user->profile_picture,
                            'username' => $post->user->username,
                            'action_type' => $post->action_type,
                            'content' => $post->content,
                            'type' => $post->type,
                            'playlist_id' => $post->playlist_id,
                            'name' => $post->playlist->name,
                            'owner_id' => $post->playlist->user_id,
                            'owner_name' => $post->playlist->user->username,
                            'description' => $post->playlist->description,
                            'image' => $post->playlist->image,
                            'is_liked' => PostLike::where('post_id', $post->id)->where('user_id', request()->user()->id)->exists(),
                            'like_count' => PostLike::where('post_id', $post->id)->count(),
                            'comment_count' => Comment::where('post_id', $post->id)->count(),
                            'released_at' => $post->created_at,
                        ];
                    case 'text':
                        return [
                            'id' => $post->id,
                            'user_id' => $post->user_id,
                            'profile_picture' => $post->user->profile_picture,
                            'username' => $post->user->username,
                            'action_type' => $post->action_type,
                            'content' => $post->content,
                            'type' => $post->type,
                            'is_liked' => PostLike::where('post_id', $post->id)->where('user_id', request()->user()->id)->exists(),
                            'like_count' => PostLike::where('post_id', $post->id)->count(),
                            'comment_count' => Comment::where('post_id', $post->id)->count(),
                            'released_at' => $post->created_at,
                        ];
                }
            });

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
                'post_type' => 'in:song,album,artist,playlist,text',
                'action_type' => 'in:shared,recommended,not_recommended',
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Verificar si el id es correcto dependiendo del tipo de post
            switch ($request->get('post_type')) {
                case 'song':
                    if (!Song::find($request->get('id'))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Song not found'
                        ], 404);
                    }
                    break;
                case 'album':
                    if (!Album::find($request->get('id'))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Album not found'
                        ], 404);
                    }
                    break;
                case 'artist':
                    if (!Artist::where('user_id', $request->get('id'))->exists()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Artist not found'
                        ], 404);
                    }
                    break;
                case 'playlist':
                    if (!Playlist::find($request->get('id'))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Playlist not found'
                        ], 404);
                    }
                    break;
            }

            // Crear un nuevo post con el user_id del usuario autenticado
            switch ($request->get('post_type')) {
                case 'song':
                    $post = new Post();
                    $post->user_id = $request->user()->id;
                    $post->content = $request->get('content');
                    $post->song_id = $request->get('id');
                    $post->action_type = $request->get('action_type');
                    $post->save();
                    break;
                case 'album':
                    $post = new Post();
                    $post->user_id = $request->user()->id;
                    $post->content = $request->get('content');
                    $post->album_id = $request->get('id');
                    $post->action_type = $request->get('action_type');
                    $post->save();
                    break;
                case 'artist':
                    $post = new Post();
                    $post->user_id = $request->user()->id;
                    $post->content = $request->get('content');
                    $post->artist_id = Artist::where('user_id', $request->get('id'))->first()->id;
                    $post->action_type = $request->get('action_type');
                    $post->save();
                    break;
                case 'playlist':
                    $post = new Post();
                    $post->user_id = $request->user()->id;
                    $post->content = $request->get('content');
                    $post->playlist_id = $request->get('id');
                    $post->action_type = $request->get('action_type');
                    $post->save();
                    break;
                case 'text':
                    $post = new Post();
                    $post->user_id = $request->user()->id;
                    $post->content = $request->get('content');
                    $post->action_type = $request->get('action_type');
                    $post->save();
                    break;
            }

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
    public function show(Request $request, string $user_id)
    {
        try {
            // Obtener todos los posts del usuario en orden
            $posts = Post::where('user_id', $user_id)->orderBy('created_at', 'desc')->get();


            // Ootener el tipo de post 
            foreach ($posts as $post) {
                switch (true) {
                    case $post->song_id:
                        $post->type = 'song';
                        break;
                    case $post->album_id:
                        $post->type = 'album';
                        break;
                    case $post->artist_id:
                        $post->type = 'artist';
                        break;
                    case $post->playlist_id:
                        $post->type = 'playlist';
                        break;
                    default:
                        $post->type = 'text';
                        break;
                }
            }

            // Organizar la información que se va a devolver
            $posts = $posts->map(function ($post) {
                switch ($post->type) {
                    case 'song':
                        return [
                            'id' => $post->id,
                            'user_id' => $post->user_id,
                            'profile_picture' => $post->user->profile_picture,
                            'username' => $post->user->username,
                            'action_type' => $post->action_type,
                            'content' => $post->content,
                            'type' => $post->type,
                            'song_id' => $post->song_id,
                            'title' => $post->song->title,
                            'duration' => $post->song->time,
                            'genre' => $post->song->genre,
                            'url_song' => $post->song->url_song,
                            'album_id' => $post->song->album->id,
                            'album_title' => $post->song->album->title,
                            'icon' => $post->song->album->icon,
                            'artist_id' => $post->song->artist->user_id,
                            'artist_name' => $post->song->artist->user->username,
                            'is_liked' => PostLike::where('post_id', $post->id)->where('user_id', request()->user()->id)->exists(),
                            'like_count' => PostLike::where('post_id', $post->id)->count(),
                            'comment_count' => Comment::where('post_id', $post->id)->count(),
                            'released_at' => $post->created_at,
                        ];
                    case 'album':
                        return [
                            'id' => $post->id,
                            'user_id' => $post->user_id,
                            'profile_picture' => $post->user->profile_picture,
                            'username' => $post->user->username,
                            'action_type' => $post->action_type,
                            'content' => $post->content,
                            'type' => $post->type,
                            'album_id' => $post->album_id,
                            'title' => $post->album->title,
                            'description' => $post->album->description,
                            'icon' => $post->album->icon,
                            'artist_id' => $post->album->artist->user_id,
                            'artist_name' => $post->album->artist->user->username,
                            'is_liked' => PostLike::where('post_id', $post->id)->where('user_id', request()->user()->id)->exists(),
                            'like_count' => PostLike::where('post_id', $post->id)->count(),
                            'comment_count' => Comment::where('post_id', $post->id)->count(),
                            'released_at' => $post->created_at,
                        ];
                    case 'artist':
                        return [
                            'id' => $post->id,
                            'user_id' => $post->user_id,
                            'profile_picture' => $post->user->profile_picture,
                            'username' => $post->user->username,
                            'action_type' => $post->action_type,
                            'content' => $post->content,
                            'type' => $post->type,
                            'artist_id' => $post->artist->user_id,
                            'name' => $post->artist->user->username,
                            'about' => $post->artist->about,
                            'artist_profile_picture' => $post->artist->user->profile_picture,
                            'artist_profile_banner' => $post->artist->user->profile_banner,
                            'is_liked' => PostLike::where('post_id', $post->id)->where('user_id', request()->user()->id)->exists(),
                            'like_count' => PostLike::where('post_id', $post->id)->count(),
                            'comment_count' => Comment::where('post_id', $post->id)->count(),
                            'released_at' => $post->created_at,
                        ];
                    case 'playlist':
                        return [
                            'id' => $post->id,
                            'user_id' => $post->user_id,
                            'profile_picture' => $post->user->profile_picture,
                            'username' => $post->user->username,
                            'action_type' => $post->action_type,
                            'content' => $post->content,
                            'type' => $post->type,
                            'playlist_id' => $post->playlist_id,
                            'name' => $post->playlist->name,
                            'owner_id' => $post->playlist->user_id,
                            'owner_name' => $post->playlist->user->username,
                            'description' => $post->playlist->description,
                            'image' => $post->playlist->image,
                            'is_liked' => PostLike::where('post_id', $post->id)->where('user_id', request()->user()->id)->exists(),
                            'like_count' => PostLike::where('post_id', $post->id)->count(),
                            'comment_count' => Comment::where('post_id', $post->id)->count(),
                            'released_at' => $post->created_at,
                        ];
                    case 'text':
                        return [
                            'id' => $post->id,
                            'user_id' => $post->user_id,
                            'profile_picture' => $post->user->profile_picture,
                            'username' => $post->user->username,
                            'action_type' => $post->action_type,
                            'content' => $post->content,
                            'type' => $post->type,
                            'is_liked' => PostLike::where('post_id', $post->id)->where('user_id', request()->user()->id)->exists(),
                            'like_count' => PostLike::where('post_id', $post->id)->count(),
                            'comment_count' => Comment::where('post_id', $post->id)->count(),
                            'released_at' => $post->created_at,
                        ];
                }
            });

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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'content' => 'string',
                'action_type' => 'in:shared,recommended,not_recommended',
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Verificar si el post existe
            $post = Post::find($id);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            // Verificar si el usuario autenticado es el propietario del post
            if ($request->user()->id !== $post->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to update this post'
                ], 403);
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
    public function destroy(Request $request, string $id): JsonResponse
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

            // Verificar si el usuario autenticado es el propietario del post
            if ($request->user()->id !== $post->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this post'
                ], 403);
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
