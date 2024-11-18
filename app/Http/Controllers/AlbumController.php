<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Album;
use App\Models\Artist;
use Illuminate\Support\Facades\Validator;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Verificar si el usuario es un artista con el rol de artista en users
            if ($request->user()->role !== 'artist') {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Obtener el artista con el user_id
            $artist = Artist::where('user_id', $request->user()->id)->first();

            // Obtener todos los álbumes del artista con el user_id y saber cuantas canciones tiene 
            $albums = Album::where('artist_id', $artist->id)->withCount('songs')->get();

            // Si no hay álbumes
            if ($albums->count() === 0) {
                return response()->json([
                    'message' => 'No albums found'
                ], 404);
            }

            return response()->json($albums);
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

            // Verificar si el usuario es un artista con el rol de artista en users
            if ($request->user()->role !== 'artist') {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'release_date' => 'required',
                'description' => 'required',
                'icon' => 'required'
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // traer todos los datos del artista con el user_id
            $artist = Artist::where('user_id', $request->user()->id)->first();

            //print de artist


            // Si el artista no existe
            if (!$artist) {
                return response()->json([
                    'message' => 'Artist not found'
                ], 404);
            }

            // Validar si el artista está verificado 
            if (!$artist->verified) {
                return response()->json([
                    'message' => 'Artist not verified'
                ], 400);
            }

            // Validar si el título del álbum ya existe
            $album = Album::where('title', $request->title)->first();
            if ($album) {
                return response()->json([
                    'message' => 'Album already exists'
                ], 400);
            }

            // Crear un nuevo álbum
            $album = new Album();
            $album->title = $request->title;
            $album->release_date = $request->release_date;
            $album->description = $request->description;
            $album->icon = $request->icon;
            $album->artist_id = $artist->id;
            $album->save();

            return response()->json([
                'success' => true,
                'data' => $album
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            // Validar que se esté ingresando un ID
            if (!$id) {
                return response()->json([
                    'message' => 'ID is required'
                ], 400);
            }

            // Buscar el álbum por el ID
            $album = Album::find($id);

            // Si el álbum no existe
            if (!$album) {
                return response()->json([
                    'message' => 'Album not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $album
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {

            // Verificar si el usuario es un artista con el rol de artista en users
            if ($request->user()->role !== 'artist') {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'title' => 'string|max:255',
                'release_date' => 'date',
                'description' => 'string',
                'icon' => 'string',
                'is_active' => 'boolean'
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }
            // Validar que se esté ingresando un ID
            if (!$id) {
                return response()->json([
                    'message' => 'ID is required'
                ], 400);
            }

            // Comprobar si el álbum existe
            $album = Album::find($id);

            // Si el álbum no existe
            if (!$album) {
                return response()->json([
                    'message' => 'Album not found'
                ], 404);
            }

            // Actualizar el álbum
            $album->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $album
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
    public function destroy(Request $request, string $id)
    {
        try {

            // Verificar si el usuario es un artista con el rol de artista en users
            if ($request->user()->role !== 'artist') {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Validar que se esté ingresando un ID
            if (!$id) {
                return response()->json([
                    'message' => 'ID is required'
                ], 400);
            }

            // Buscar el álbum por el ID
            $album = Album::find($id);

            // Si el álbum no existe
            if (!$album) {
                return response()->json([
                    'message' => 'Album not found'
                ], 404);
            }

            // Eliminar el álbum
            $album->delete();

            return response()->json([
                'success' => true,
                'data' => $album
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAlbumByTitle(Request $request, string $title): JsonResponse
    {
        try {
            // Buscar el álbum por el nombre y contar las canciones
            $album = Album::where('title', $title)->withCount('songs')->first();

            //Si el álbum no existe
            if (!$album) {
                return response()->json([
                    'message' => 'Album not found'
                ], 404);
            }

            // Retornar el álbum
            return response()->json($album);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAlbumsByUserId(string $userId): JsonResponse
    {
        // Obtener los álbumes de un artista
        try {
            // Validar que se esté ingresando un UUID
            if (!$userId) {
                return response()->json([
                    'message' => 'User ID is required'
                ], 400);
            }

            // Obtener el artista
            $artist = Artist::where('user_id', $userId)->first();

            // Si el artista no existe
            if (!$artist) {
                return response()->json([
                    'message' => 'Artist not found'
                ], 404);
            }

            // Obtener los álbumes del artista que estén activos paginados
            $albums = Album::where('artist_id', $artist->id)->where('is_active', true)->withCount('songs')->paginate(5);

            return response()->json([
                'data' => $albums->items(),  // Retorna solo los álbumes de la página actual
                'pagination' => [
                    'current_page' => $albums->currentPage(),
                    'per_page' => $albums->perPage(),
                    'total' => $albums->total(),
                    'last_page' => $albums->lastPage(),
                    'next_page_url' => $albums->nextPageUrl(),
                    'prev_page_url' => $albums->previousPageUrl(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
