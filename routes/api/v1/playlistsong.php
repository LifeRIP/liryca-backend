<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

// Playlist //

Route::middleware('auth:api')->group(function () {
    Route::get('playlists', 'PlaylistController@index');
    Route::get('playlists/{id}', 'PlaylistController@show');
    Route::post('playlists', 'PlaylistController@store');
    Route::put('playlists/{id}', 'PlaylistController@update');
    Route::delete('playlists/{id}', 'PlaylistController@destroy');
});
