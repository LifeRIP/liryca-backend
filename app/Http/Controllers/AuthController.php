<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        //validate the register request:
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'birthday' => 'required|date',
            'country' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //to create an user on the databse and generate UUID:
        $user = new User();
        $user->id = Str::uuid();
        $user->username = $request->username;
        $user->birthday = $request->birthday;
        $user->country = $request->country;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'user';
        $user->description = '';
        $user->register_date = date('Y-m-d H:i:s');
        $user->statement_of_account = 1;

        $user->save();

        return response()->json(['message' => 'User created successfully'], 201);
    }


    public function login(Request $request)
    {
        //validate the login request:
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //to check if the user exists on the database:
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }


        return response()->json(['Logged'], 200);
    }
}
