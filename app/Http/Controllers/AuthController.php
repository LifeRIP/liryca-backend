<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;

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
            'is_active' => true,
        ]);

        // Enviar correo de verificación
        $user->sendEmailVerificationNotification();

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
        // Validar la petición de inicio de sesión
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
            'remember' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Autenticar al usuario
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Recordar el inicio de sesión del usuario
        $remember = $request->remember ?? false;

        Auth::login($user, $remember);

        // Crear un token de acceso para el usuario
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'remember' => $remember
        ], 201);
    }

    public function logout(Request $request)
    {
        // Revocar el token de acceso actual del usuario
        $request->user()->currentAccessToken()->delete();

        // Opcional: Revocar todos los tokens de acceso del usuario
        // $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function sendEmail(Request $request)
    {
        // Verificar si el usuario ya verificó su correo electrónico
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        // Enviar correo de verificación
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Email verification link sent']);
    }

    public function verifyEmail(Request $request)
    {
        // Verificar si el usuario ya verificó su correo electrónico
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        // Verificar el correo electrónico del usuario
        if ($request->user()->markEmailAsVerified()) {
            return response()->json(['message' => 'Email verified']);
        }

        return response()->json(['message' => 'Invalid verification link'], 400);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Método para manejar la respuesta de Google
    public function handleGoogleCallback()
    {
        #$user_google = Socialite::driver("google")->stateless()->user(); 
        $user_google = Socialite::driver("google")->user();

        $user = user::updateOrCreate([
            'google_id' => $user_google->id, //create a new field on users table called "googel_id"
        ], [
            'name' => $user_google->name,
            'email' => $user_google->email,
            'username' => $user_google->nickname ?? $user_google->name,
            'birthday' => '2000-01-01', //default values for the fields
            'country' => 'Unknown', //default values for the fields
        ]);

        Auth::login($user, true); //login the user
        return redirect('/dashboard'); //redirect to another page
    }
}
