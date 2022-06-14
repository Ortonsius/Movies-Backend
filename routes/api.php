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
    Route::post('/movie/list', [MovieCon::class,"getAllMovies"]);
    Route::post('/movie/add', [MovieCon::class,"addMovie"]);
    Route::post('/movie/edit', [MovieCon::class,"editMovie"]);
    Route::post('/movie/del', [MovieCon::class,"delMovie"]);
});

// Authentication API
Route::post('/login', [AuthCon::class,"login"]);
Route::post('/logout', [AuthCon::class,"logout"]);
Route::post('/register', [AuthCon::class,"register"]);

// User API
Route::post('/show', [MovieCon::class,"OnShow"]);
Route::post('/coming-soon', [MovieCon::class,"ComingSoon"]);
Route::post('/fav/add', [MovieCon::class,"setFavoriteMovie"]);
Route::post('/fav/get', [MovieCon::class,"getFavoriteMovie"]);
Route::post('/fav/del', [MovieCon::class,"delFavoriteMovie"]);
Route::post('/movie/{mid}/{rate}', [MovieCon::class,"rateMovie"]);