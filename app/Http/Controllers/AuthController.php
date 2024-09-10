<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\support\Facades\auth;
use App\Models\User;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        //validate the register request:
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'birthday' => 'required|date',
            'country' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //to create an user on the databse:
        $user = User::create([
            'name' => $fields['name'],
            'username' => $fields['username'],
            'birthday' => $fields['birthday'],
            'country' => $fields['country'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role' => 'user', //deafult role assigned to the user
            'register_date' => now(), //today
            'statement_of_account' => true //default value

          

        ]);

        $user->save();
        return User::all();

        //to create a user token:
        $token = $user->createToken('myauthtoken')->plainTextToken;

        //this function will associate the user and his token:
        $response = [
            'user' => $user,
            'token' => $token
        ];

        //return an http response that includes the array with the user and the token:
        return response($response, 201);
    }


    public function login(Request $request)
    {
        //validate the login request:
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
            //verify if the user exists and if it doesn't, show an error message:
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'Login successful', 'token' => $token], 200);
    }
    

  
}
