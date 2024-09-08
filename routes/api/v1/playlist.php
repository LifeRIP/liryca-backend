<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

// Playlist //

Route::post('/add', 'App\Http\Controllers\PlaylistController@add_playlist');
Route::get('/get', 'App\Http\Controllers\PlaylistController@get_playlists');
Route::put('/update', 'App\Http\Controllers\PlaylistController@update_playlist');
Route::delete('/delete', 'App\Http\Controllers\PlaylistController@delete_playlist');
