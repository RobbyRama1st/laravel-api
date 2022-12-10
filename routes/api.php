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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->get('/', function () {
       return "White Lizard Api";
    });

    $api->group(['prefix' => 'auth'], function ($api) {
        $api->post('/signup', 'App\Http\Controllers\Api\AuthController@signUp');
        $api->post('/login', 'App\Http\Controllers\Api\AuthController@login');
        $api->post('/refresh-token', 'App\Http\Controllers\Api\AuthController@refreshToken');
        $api->post('/logout', 'App\Http\Controllers\Api\AuthController@logout');
    });

});
