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

Route::middleware('auth:api')->get('/user', function (Request $request) {
	//$user = Auth::user();
//return $user->only(['id', 'name', 'email']);
	return response()->json([
			'response' => $request->user()->only(['id', 'name', 'email']),
			'status' => true
	]) ->header('Content-Type', 'application/json');
});

//Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'AuthController@login');
//Route::post('refresh', 'Auth\LoginController@refresh');
//Route::post('login', 'LoginController@login');
/*Route::group(['middleware' => 'auth:api'], function() {
Route::post('logout', 'Auth\LoginController@logout');
	Route::get('articles', 'ArticleController@index');
	Route::get('articles/{article}', 'ArticleController@show');
	Route::post('articles', 'ArticleController@store');
	Route::put('articles/{article}', 'ArticleController@update');
	Route::delete('articles/{article}', 'ArticleController@delete');
});*/
