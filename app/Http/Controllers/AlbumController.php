<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Artist;
use App\Models\Album;

class AlbumController extends Controller
{
    // añador un nuevo álbum
    public function add_album(Request $request)
    {
        //Usar un try-catch para toda la función

        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'artistID' => 'required',
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

            // Comprobar si el artista existe 
            $artist = Artist::find($request->artistID);
            if (!$artist) {
                return response()->json([
                    'message' => 'Artist not found'
                ], 404);
            }

            // Validar si el artista es un artista verificado
            if (!$artist->verified) {
                return response()->json([
                    'message' => 'Artist is not verified'
                ], 400);
            }

            //Validar si el titulo del album ya existe
            $album = Album::where('title', $request->title)->first();
            if ($album) {
                return response()->json([
                    'message' => 'Album already exists'
                ], 400);
            }


            // Crear un nuevo álbum
            $album = new Album();
            $album->artist_id = $request->artistID;
            $album->title = $request->title;
            $album->release_date = $request->release_date;
            $album->description = $request->description;
            $album->icon = $request->icon;
            $album->is_active = 1;
            $album->save();

            // Retornar un mensaje de éxito y el álbum creado
            return response()->json([
                'message' => 'Album created successfully',
                'album' => $album
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obterner todos los álbumes del artista
    public function get_albums(Request $request)
    {
        try {

            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'artistID' => 'required'
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Comprobar si el artista existe
            $artist = Artist::find($request->artistID);
            if (!$artist) {
                return response()->json([
                    'message' => 'Artist not found'
                ], 404);
            }

            // Obtener todos los álbumes del artista que estén activos del mas reciente al mas antiguo
            $albums = Album::where('artist_id', $request->artistID)->where('is_active', 1)->orderBy('release_date', 'desc')->get();

            // Retornar un mensaje de éxito y los álbumes del artista
            return response()->json([
                'message' => 'Albums found',
                'albums' => $albums
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Modificar un album 
    public function update_album(Request $request)
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'albumID' => 'required',
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

            // Comprobar si el álbum existe
            $album = Album::find($request->albumID);
            if (!$album) {
                return response()->json([
                    'message' => 'Album not found'
                ], 404);
            }

            // Actualizar los campos del álbum
            if ($request->title) {
                $album->title = $request->title;
            }
            if ($request->release_date) {
                $album->release_date = $request->release_date;
            }
            if ($request->description) {
                $album->description = $request->description;
            }
            if ($request->icon) {
                $album->icon = $request->icon;
            }
            $album->save();

            // Retornar un mensaje de éxito y el álbum actualizado
            return response()->json([
                'message' => 'Album updated successfully',
                'album' => $album
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminar un álbum
    public function delete_album(Request $request)
    {
        try {
            // Validar los campos requeridos
            $validator = Validator::make($request->all(), [
                'albumID' => 'required'
            ]);

            // Comprobar si la validación falla
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Comprobar si el álbum existe y está activo

            $album = Album::find($request->albumID);
            if (!$album or $album->is_active == 0) {
                return response()->json([
                    'message' => 'Album not found'
                ], 404);
            }

            // Eliminar el álbum

            $album->is_active = 0;

            // Retornar un mensaje de éxito
            return response()->json([
                'message' => 'Album deleted successfully'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
