<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
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
            'password' => ['required', 'string', PasswordRule::default(), 'confirmed'],
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
            'description' => '',
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
                PasswordRule::default(),
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

        Auth::login($user, $remember); #$remember);

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

    public function sendEmailRecovery(Request $request)
    {

        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $status = Password::sendResetLink(
            request()->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }

    public function recover($token) {}

    public function savePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successfully'], 200)
            //? redirect()->route('dashboard')->with('status', __($status)) redirect to another page, usually main page
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Método para manejar la respuesta de Google
    public function handleGoogleCallback()
    {
        $user_google = Socialite::driver("google")->user();
        //$user_google = Socialite::driver("google")->stateless()->user(); 
        $user = User::where('external_id', $user_google->id)->orWhere('email', $user_google->email)->first();
        if ($user) {

            return response()->json(['error' => 'El correo ya esta registrado.'], 400);
        }


        $user = user::updateOrCreate([
            'external_id' => $user_google->id, //create a new field on users table called "googel_id"
        ], [
            'name' => $user_google->name,
            'email' => $user_google->email,
            'username' => $user_google->nickname ?? $user_google->name,
            'external_id' => $user_google->id,
            'external_auth' => 'google',
            'birthday' => '2000-01-01', //default values for the fields
            'country' => 'Unknown', //default values for the fields
        ]);

        Auth::login($user, true); //login the user
        return redirect('/dashboard'); //redirect to another page
    }

    // Método para redirigir a facebook auth
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {

        $user_facebook = Socialite::driver("facebook")->user();

        $user = User::where('external_id', $user_facebook->id)->orWhere('email', $user_facebook->email)->first(); //verifica si el facebook_id o email existen en la base de datos

        $user = User::create([
            'name' => $user_facebook->name,
            'email' => $user_facebook->email,
            'username' => $user_facebook->nickname ?? $user_facebook->name,
            'password' => bcrypt(Str::random(16)), // a default password on password field
            'external_id' => $user_facebook->id,
            'external_auth' => 'facebook',
            'birthday' => '2000-01-01',
            'country' => 'Unknown',
        ]);

        $user = user::updateOrCreate([
            'external_id' => $user_facebook->id, //create a new field on users table called "external_id"
        ], [
            //'name' => $user_facebook->name,
            'email' => $user_facebook->email,
            'username' => $user_facebook->nickname ?? $user_facebook->name,
            'birthday' => '2000-01-01', //default values for the fields
            'country' => 'Unknown', //default values for the fields
        ]);


        Auth::login($user, true); //login the user
        return redirect('/dashboard'); //redirect to another page

    }

    // Método para redirigir a github auth
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGithubCallback()
    {
        $user_github = socialite::driver('github')->user();

        $user = User::where('external_id', $user_github->id)->orWhere('email', $user_github->email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $user_github->name,
                'email' => $user_github->email,
                'username' => $user_github->nickname ?? $user_github->name,
                'password' => bcrypt(Str::random(16)), // a default password on password field
                'external_id' => $user_github->id,
                'external_auth' => 'github',
                'birthday' => '2000-01-01',
                'country' => 'Unknown',
            ]);
        }

        $user = User::updateorCreate(
            [
                'external_id' => $user_github->id,
            ],
            [
                //'name' => $user_github->name,
                'email' => $user_github->email,
                'username' => $user_github->nickname ?? $user_github->name,
                'birthday' => '2000-01-01',
                'country' => 'Unknown',

            ]
        );
        Auth::login($user, true); //login the user
        return redirect('/dashboard'); //redirect to another page
    }
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $user_provider = Socialite::driver($provider)->user();
        $user = User::where('external_id', $user_provider->id)->orWhere('email', $user_provider->email)->first();
        if ($user) {
            return response()->json(['error' => 'Email already registered.'], 400);
        }
        $user = User::create([
            'username' => $user_provider->nickname ?? $user_provider->name,
            'email' => $user_provider->email,
            'password' => Hash::make(Str::random(16)), // a default password on password field
            'external_id' => $user_provider->id,
            'external_auth' => $provider,
            'birthday' => '2000-01-01',
            'country' => 'Unknown',
        ]);
        Auth::login($user, true); //login the user
        return redirect('/dashboard'); //redirect to another page
    }
}
