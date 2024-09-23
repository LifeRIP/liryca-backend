<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validar la petición de registro
        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique(User::class)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'country' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Crear un nuevo usuario
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'birthday' => $request->birthday,
            'role' => 'user',
            'description' => '',
            'register_date' => date('Y-m-d H:i:s'),
            'statement_of_account' => 1,
        ]);

        // Crear un token de acceso para el usuario
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }


    public function login(Request $request)
    {
        //validate the login request:
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                Password::default(),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //verificar que email y contr no sean nulos:
        if (is_null($request->email) || is_null($request->password)) {
                return response()->json(['message' => 'Email and password are required'], 400);
        }

        //to check if the user exists on the database:
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Crear un token de acceso para el usuario
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        // Optionally, you can also revoke all tokens for the user
        // $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }


    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    
    // Método para manejar la respuesta de Google
    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->stateless()->user();
    
        // Lógica para manejar el usuario autenticado
        // Por ejemplo, buscar o crear el usuario en la base de datos
        $existingUser = User::where('email', $user->getEmail())->first();
    
        if ($existingUser) {
            Auth::login($existingUser);
        } else {
            $newUser = User::create([
                'username' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => Hash::make(Str::random(24)), // Generar una contraseña aleatoria
            ]);
    
            Auth::login($newUser);
        }
    
        return redirect('/home'); // Redirigir a la página de inicio o donde desees
    }

}

















/*
public function logout(Request $request)
    {
        //laravel php method to logout an user:

        if (Auth::check()){ //it doesn't care what type of autentication are you using, the method will validate it.

            Auth::logout();

            $request->session()->invalidate();


            return response()->json(['message' => 'Logged out'], 200);

        } else{

            
            return response()->json(['message' => 'No one is logged'], 400);
        }
       
    }    
}
*/