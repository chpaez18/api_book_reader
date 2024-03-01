<?php

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

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
return $request->user();
}); */

Route::middleware('guest')->group(function () {
    Route::post('register', 'AuthController@register')->name('register');
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('refresh-token', 'AuthController@refreshToken')->name('refreshToken');
    Route::post('forgot-password', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('validate-reset-token', 'AuthController@validateResetToken');
    Route::post('reset-password', 'AuthController@resetPassword')->name('resetPassword');

    //google login
    Route::get('auth/get-google-login-url', 'App\Http\Controllers\Auth\GoogleController@googleLoginUrl');
    Route::get('auth/google/callback', 'App\Http\Controllers\Auth\GoogleController@loginCallback');
    Route::post('auth/google/login', 'App\Http\Controllers\Auth\GoogleController@googleLogin');


});

Route::middleware('auth:api')->group(function () {

    Route::post('logout', 'AuthController@logout')->name('logout');

    /*-------------------------------------------------------------------------------/
     *********************  Roles and Permissions    ****************************** */
        Route::apiResource('/roles', 'App\Http\Controllers\Admin\RolController');
        Route::post('/roles/assign-permissions/{id}', 'App\Http\Controllers\Admin\RolController@assignPermissions');

        Route::apiResource('/permissions', 'App\Http\Controllers\Admin\PermissionController');
    /*-------------------------------------------------------------------------------*/


    /*-------------------------------------------------------------------------------/
     *********************  Users Administration ********************************** */
        Route::get('/users/get-info', 'App\Http\Controllers\Admin\UserController@getInfo');
        Route::get('/users/get-photos', 'App\Http\Controllers\Admin\UserController@getPhotos');
        Route::apiResource('/users', 'App\Http\Controllers\Admin\UserController');
        Route::delete('/users', 'App\Http\Controllers\Admin\UserController@destroy');
        Route::put('/users/update-password/{id}', 'App\Http\Controllers\Admin\UserController@updatePassword');
        Route::post('/users/assign-role/{id}', 'App\Http\Controllers\Admin\UserController@assignRole');

    /*-------------------------------------------------------------------------------*/

    /*-------------------------------------------------------------------------------/
     *********************  Drive Endpoints ************************************** */
        //Route::get('/auth/google/refresh-token', 'App\Http\Controllers\Auth\GoogleController@refreshGoogleToken');
        Route::get('/drive', 'App\Http\Controllers\DriveController@getDrive');
        Route::post('/drive/upload', 'App\Http\Controllers\DriveController@uploadFile');
        Route::get('/drive/get-folder-files', 'App\Http\Controllers\DriveController@getFolderFiles');
        Route::get('/drive/delete/{id}', 'App\Http\Controllers\DriveController@deleteFile');
    /*-------------------------------------------------------------------------------*/

    /*-------------------------------------------------------------------------------/
     *********************  Book Administration ********************************** */
        Route::get('/book/info', 'App\Http\Controllers\BookController@getInfo');
        Route::get('/book/quotes', 'App\Http\Controllers\BookController@getQuotes');
        Route::post('/book/save-anecdote', 'App\Http\Controllers\BookController@saveAnecdote');
    /*-------------------------------------------------------------------------------*/

    /*-------------------------------------------------------------------------------/
     *********************  Codes Administration ********************************** */
        Route::get('/code/index', 'App\Http\Controllers\CodeController@getCodes');
        Route::post('/code/generate', 'App\Http\Controllers\CodeController@generate');
        Route::post('/code/change-status', 'App\Http\Controllers\CodeController@changeStatus');
        Route::post('/code/validate-code', 'App\Http\Controllers\CodeController@validateCode');
        Route::delete('/code/delete/{id}', 'App\Http\Controllers\CodeController@deleteCode');
    /*-------------------------------------------------------------------------------*/


});


