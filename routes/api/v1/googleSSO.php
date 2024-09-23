<?php

use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\AuthController;
//use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;



//route for google login
Route::get('/login-google', function (request $request){
    return Socialite::driver("google")->redirect();
});

//route for google callback
Route::get('/google-callback', function (request $request){
    
    $user_google = Socialite::driver("google")->stateless()->user();

    $user = user::updateOrCreate([
        'google_id' => $user_google->id, //create a new field on users table called "googel_id"
    ],[
        'name' => $user_google->name,
        'email' => $user_google->email,
        'username' => $user_google->nickname ?? $user_google->name,
        'birthday' => '2000-01-01', //default values for the fields
        'country' => 'Unknown', //default values for the fields
    ]);

    Auth::login($user, true); //login the user
    return redirect('/dashboard'); //redirect to another page
    
});