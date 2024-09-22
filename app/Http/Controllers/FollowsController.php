<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Follow;
use App\Models\User;


class FollowsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validar los datos
        $validator = Validator::make($request->all(), [
            'followed_id' => 'required|uuid|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //Comprobar si el usuario ya sigue al usuario
        $follow = Follow::where('follower_id', $request->user()->id)
            ->where('followed_id', $request->followed_id)
            ->first();

        if ($follow) {
            return response()->json(['message' => 'Ya sigues a este usuario'], 400);
        }

        //Crear el nuevo seguidor
        $follow = new Follow();
        $follow->follower_id = $request->user()->id;
        $follow->followed_id = $request->followed_id;
        $follow->save();

        return response()->json(['message' => 'Usuario seguido correctamente'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //Obtener los seguidores del usuario autenticado
        $follows = Follow::where('follower_id', $request->user()->id)->get();

        //Obtener el numero de seguidores
        $count_followers = Follow::where('followed_id', $request->user()->id)->count();

        //Obtener el id, username, icono y banner de los usuarios seguidos
        $users = [];
        foreach ($follows as $follow) {
            $user = User::select('id', 'username', 'profile_picture', 'profile_banner')
                ->where('id', $follow->followed_id)
                ->first();
            $users[] = $user;
        }

        //Obtener los seguidos del usuario autenticado
        $followed = Follow::where('followed_id', $request->user()->id)->get();

        //Obtener el numero de seguidos
        $count_followed = Follow::where('follower_id', $request->user()->id)->count();

        //Obtener el id, username, icono y banner de los seguidores
        $followers = [];
        foreach ($followed as $follow) {
            $follower = User::select('id', 'username', 'profile_picture', 'profile_banner')
                ->where('id', $follow->follower_id)
                ->first();
            $followers[] = $follower;
        }

        return response()->json([
            'count_followers' => $count_followers,
            'followers' => $followers,
            'count_followed' => $count_followed,
            'followed' => $users
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //Validar los datos
        $validator = Validator::make($request->all(), [
            'followed_id' => 'required|uuid|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //Comprobar si el usuario sigue al usuario
        $follow = Follow::where('follower_id', $request->user()->id)
            ->where('followed_id', $request->followed_id);

        if (!$follow) {
            return response()->json(['message' => 'No sigues a este usuario'], 400);
        }

        //Eliminar la relacion de seguidor
        $follow->delete();

        return response()->json(['message' => 'Usuario dejado de seguir correctamente'], 200);
    }
}
