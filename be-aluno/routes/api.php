<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('students', 'App\Http\Controllers\AlunoController@index');

Route::get('students/show', 'App\Http\Controllers\AlunoController@show');

Route::post('/students/store', 'App\Http\Controllers\AlunoController@store');

Route::put('/students/{RA}', 'App\Http\Controllers\AlunoController@update');

Route::delete('/students/{RA}', 'App\Http\Controllers\AlunoController@destroy');
