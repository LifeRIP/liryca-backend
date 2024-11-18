<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Follow;
use App\Models\User;

use function Pest\Laravel\get;

class FollowsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            //Validar los datos
            $validator = Validator::make($request->all(), [
                'following_id' => 'required|uuid|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            //Comprobar si el usuario ya sigue al usuario
            $follow = Follow::where('follower_id', $request->user()->id)
                ->where('following_id', $request->following_id)
                ->first();

            if ($follow) {
                return response()->json(['message' => 'Ya sigues a este usuario'], 400);
            }

            //Crear el nuevo seguidor
            $follow = new Follow();
            $follow->follower_id = $request->user()->id;
            $follow->following_id = $request->following_id;
            $follow->save();

            return response()->json(['message' => 'Usuario seguido correctamente'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al seguir al usuario'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {

        try {
            //Obtener los seguidores del usuario autenticado
            $follows = Follow::where('follower_id', $request->user()->id)->get();

            //Obtener el numero de seguidores
            $count_followers = Follow::where('following_id', $request->user()->id)->count();

            //Obtener el id, username, icono y banner de los usuarios seguidos
            $users = [];
            foreach ($follows as $follow) {
                $user = User::select('id', 'username', 'profile_picture', 'profile_banner')
                    ->where('id', $follow->following_id)
                    ->first();
                $users[] = $user;
            }

            //Obtener los seguidos del usuario autenticado
            $following = Follow::where('following_id', $request->user()->id)->get();

            //Obtener el numero de seguidos
            $count_following = Follow::where('follower_id', $request->user()->id)->count();

            //Obtener el id, username, icono y banner de los seguidores
            $followers = [];
            foreach ($following as $follow) {
                $follower = User::select('id', 'username', 'profile_picture', 'profile_banner')
                    ->where('id', $follow->follower_id)
                    ->first();
                $followers[] = $follower;
            }

            return response()->json([
                'count_followers' => $count_followers,
                'followers' => $followers,
                'count_following' => $count_following,
                'following' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los seguidores'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $following_id)
    {
        try {
            // Comprobar si el usuario sigue al usuario
            $follow = Follow::where('follower_id', $request->user()->id)
                ->where('following_id', $following_id);

            if (!$follow) {
                return response()->json(['message' => 'No sigues a este usuario'], 400);
            }

            // Dejar de seguir al usuario
            $follow->delete();

            return response()->json(['message' => 'Usuario dejado de seguir correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al dejar de seguir al usuario'], 500);
        }
    }

    public function followUnit(Request $request, string $following_id)
    {

        //validar si el usuario es seguido
        try {

            //Validar los datos
            $validator = Validator::make(['following_id' => $following_id], [
                'following_id' => 'required|uuid|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $follow = Follow::where('follower_id', $request->user()->id)
                ->where('following_id', $following_id)
                ->first();

            //retornar variable follow con boolean
            if (!$follow) {
                return response()->json(['follow' => false], 200);
            }

            return response()->json(['follow' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al comprobar si sigues al usuario'], 500);
        }
    }
}
