<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/oauth2/authorize', [\App\Http\Controllers\OAuth2Controller::class, 'authorize']);
Route::post('/oauth2/authorize', [\App\Http\Controllers\OAuth2Controller::class, 'login']);

Route::post('oauth2/confirm', [\App\Http\Controllers\OAuth2Controller::class, 'grantAuthorization']);
Route::delete('oauth2/confirm', [\App\Http\Controllers\OAuth2Controller::class, 'denyAuthorization']);

Route::post('oauth2/token', [\App\Http\Controllers\OAuth2Controller::class, 'token']);


