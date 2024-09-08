<?php

use Illuminate\Support\Facades\Route;

// TEST //
Route::get('/test/user', 'App\Http\Controllers\Test_Controller@add_user');
Route::get('/test/artist', 'App\Http\Controllers\Test_Controller@add_artist');
Route::get('/test/playlist', 'App\Http\Controllers\Test_Controller@add_playlist');
Route::get('/test/song', 'App\Http\Controllers\Test_Controller@add_song');
Route::get('/test/playlist_song', 'App\Http\Controllers\Test_Controller@add_playlist_song');
Route::get('/test/shared_playlist', 'App\Http\Controllers\Test_Controller@add_shared_playlist');
Route::get('/test/album', 'App\Http\Controllers\Test_Controller@add_album');
Route::get('/test/comment', 'App\Http\Controllers\Test_Controller@add_comment');
Route::get('/test/like', 'App\Http\Controllers\Test_Controller@add_like');
Route::get('/test/follow', 'App\Http\Controllers\Test_Controller@add_follow');
Route::get('/test/post', 'App\Http\Controllers\Test_Controller@add_post');
Route::get('/test/playback_history', 'App\Http\Controllers\Test_Controller@add_playback_history');
