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
use PragmaRX\Google2FALaravel\Facade as Google2FA;
use Carbon\Carbon;

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

    public function recover ($token)
    {
       
    }

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




    public function generate2faSecret(Request $request){
        $user = $request->user();

        
       
        if($user->enable_two_factor_auth){
            return response()->json(['message' => '2FA secret already generate, enable 2fa'], 400);
        }

        $secretkey = Google2FA::generateSecretKey();
        
       /* $user->update([
            'two_factor_secret' => $secretkey,
        ]);*/
        $user->two_factor_secret = $secretkey;
        //$user->enable_two_factor_auth = true;
        $user->save();

        
        $google2FA_url = Google2FA::getQRCodeInline(
            config('app.name'),
            $user->email,
            $secretkey
        );

        $data = [
            'secret' => $secretkey,
            'google2fa_url' => $google2FA_url,
        ];

        return response()->json(['2fa initialized successfully', $data], 200);
    }

    public function enable2fa (Request $request){
        $user = $request->user();

        if($user->enable_two_factor_auth){
            return response()->json(['message' => '2FA already enabled'], 400);
        }

       
        if(! $user->two_factor_secret){
            return response()->json(['message' => '2FA secret not found'], 400);
        }

        if(! $request->validateToken()){
            return response()->json(['message' => 'Invalid verification code, please try again'], 400);
        }

        /*$request->validate([
            'token' => 'required|numeric',
        ]);*/


         $user->update([
            'enable_two_factor_auth' => true,
        ]);


        return response()->json(['message' => '2FA enabled successfully'], 200);
    }

    public function disable2fa (Request $request){
        $user = $request->user();

        if(!$user->enable_two_factor_auth){
            return response()->json(['message' => 'Not enabled'], 400);
        }

        /*if(! $request->validateToken()){
            return response()->json(['message' => 'Invalid verification code, please try again'], 400);
        }*/

        $request->validate([
            'token' => 'required|numeric',
        ]);

        $user->update([
            'two_factor_secret' => null,
            'enable_two_factor_auth' => false,
        ]);

        return response()->json(['message' => '2FA disabled successfully'], 200);
    }


    public function validateToken(Request $request): bool
    {
        $user = $request->user();

        try {
            return Google2FA::verifyKey($this->user()->two_fa_secret, $this->token);
        } catch (Exception $e) {
            report($e);
        }
    
        abort(HTTP_SERVER_ERROR, 'Unable to verify your two-factor authentication token. Please contact support.');
    }


    public function verify(Request $request)
    {
        $user = $request->user(); // Obtén el usuario autenticado
        
        // El token enviado desde Postman
        $token = $request->input('token');
        
        // Instancia de Google2FA
        $google2fa = app('pragmarx.google2fa');

        // Verificamos el token usando la clave secreta del usuario
        $isValid = $google2fa->verifyKey($user->two_factor_secret, $token);
        
        if ($isValid) {
            return response()->json([
                'message' => '2FA verification successful',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid 2FA token, please try again',
            ], 400);
        }
    }















    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Método para manejar la respuesta de Google
    public function handleGoogleCallback()
    {
        $user_google = Socialite::driver("google")->stateless()->user(); 
        //dd($user_google);
        //$user_google = Socialite::driver("google")->user();
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



    //metodo para redirigir a facebook auth
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    

    public function handleFacebookCallback()
    {
        
        $user_facebook = Socialite::driver("facebook")->stateless()->user();

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
            
    //metodo para redirigir a github auth
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }
    

    public function handleGithubCallback(){
        $user_github = socialite::driver('github')->stateless()->user();

        $user = User::where('external_id', $user_github->id)->orWhere('email', $user_github->email)->first();

        if (!$user){
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
            
        $user = User::updateorCreate([
            'external_id' => $user_github->id,]
        , [
            //'name' => $user_github->name,
            'email' => $user_github->email,
            'username' => $user_github->nickname ?? $user_github->name,
            'birthday' => '2000-01-01',
            'country' => 'Unknown',

        ]);
        Auth::login($user, true); //login the user
        return redirect('/dashboard'); //redirect to another page
    }

}



