<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

// Incluir automáticamente todas las rutas en la carpeta routes

Route::post('/add', 'App\Http\Controllers\AlbumController@add_album');
Route::get('/get', 'App\Http\Controllers\AlbumController@get_albums');
Route::put('/update', 'App\Http\Controllers\AlbumController@update_album');
Route::delete('/delete', 'App\Http\Controllers\AlbumController@delete_album');
