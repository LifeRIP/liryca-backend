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
    public function index(): JsonResponse
    {
        $albums = Album::all();
        return response()->json($albums);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'artist_id' => 'required',
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

            // Comprobar si el artista existe
            $artist = Artist::find($request->artist_id);
            if (!$artist) {
                return response()->json([
                    'message' => 'Artist not found'
                ], 404);
            }

            // Validar si el artista está verificado 
            if (!$artist->verified) {
                return response()->json([
                    'message' => 'Artist is not verified'
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
            $album = Album::create($request->all());
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
    public function show(string $Name): JsonResponse
    {
        // Buscar el álbum por el nombre
        $album = Album::where('title', $Name)->first();

        //Si el álbum no existe
        if (!$album) {
            return response()->json([
                'message' => 'Album not found'
            ], 404);
        }

        // Retornar el álbum
        return response()->json($album);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
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
    }
}
