<?php

use App\Http\Controllers\AuthCon;
use App\Http\Controllers\MovieCon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(["prefix" => "admin"],function(){
    // Admin API
    Route::get('/movie/list', [MovieCon::class,"getAllMovies"]);
    Route::get('/movie/add', [MovieCon::class,"addMovie"]);
    Route::get('/movie/edit', [MovieCon::class,"editMovie"]);
    Route::get('/movie/del', [MovieCon::class,"delMovie"]);
});

// Authentication API
Route::post('/login', [AuthCon::class,"login"]);
Route::get('/logout', [AuthCon::class,"logout"]);
Route::get('/register', [AuthCon::class,"register"]);

// User API
Route::get('/show', [MovieCon::class,"OnShow"]);
Route::get('/coming-soon', [MovieCon::class,"ComingSoon"]);
Route::get('/fav/add', [MovieCon::class,"setFavoriteMovie"]);
Route::get('/fav/get', [MovieCon::class,"getFavoriteMovie"]);
Route::get('/fav/del', [MovieCon::class,"delFavoriteMovie"]);
Route::get('/movie/{mid}/{rate}', [MovieCon::class,"rateMovie"]);