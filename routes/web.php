<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

/*
Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
*/

Route::get('/login-google', function (request $request){
    return Socialite::driver("google")->redirect();
});


Route::get('/google-callback', function (request $request){
    //dd($request->all());
    $user_google = Socialite::driver("google")->stateless()->user();

    $user = user::updateOrCreate([
        'google_id' => $user_google->id,
    ],[
        'name' => $user_google->name,
        'email' => $user_google->email,
        //'password' => bcrypt($user_google->getId()),
        'username' => $user_google->nickname ?? $user_google->name,
        'birthday' => '2000-01-01',
        'country' => 'Unknown',
    ]);

    Auth::login($user, true);
    return redirect('/dashboard');
    
});
