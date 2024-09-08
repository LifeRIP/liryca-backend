<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

// Playlist //

Route::post('/playlist/add', 'App\Http\Controllers\PlaylistController@add_playlist');
Route::get('/playlist/get', 'App\Http\Controllers\PlaylistController@get_playlists');
Route::put('/playlist/update', 'App\Http\Controllers\PlaylistController@update_playlist');
Route::delete('/playlist/delete', 'App\Http\Controllers\PlaylistController@delete_playlist');
