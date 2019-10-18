<?php

use Illuminate\Http\Request;

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

Route::prefix('auth')->group(function () {
    Route::post('register', 'Auth\AuthController@register');
    Route::post('login', 'Auth\AuthController@login');
    Route::get('refresh', 'Auth\AuthController@refresh');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('user', 'Auth\AuthController@user');
        Route::post('logout', 'Auth\AuthController@logout');
    });
});

Route::group([
    'namespace' => 'Api',
    'middleware' => ['auth:api']
], function () {
    Route::get('/', 'TransactionController@index')->name('api.transactions.index');

    Route::group([
        'prefix' => 'admin',
        'namespace' => 'Admin',
        'middleware' => ['admin'],
    ], function () {
        Route::get('/', 'TransactionController@index')->name('api.admin.transactions.index');
        Route::get('/transactions/{id}', 'TransactionController@show')->name('api.admin.transactions.show');
        Route::patch('/transactions/edit/{id}', 'TransactionController@update')->name('api.admin.transactions.update');

        Route::get('/users', 'UserController@index')->name('api.admin.users.index');
        Route::get('/users/{user}', 'UserController@show')->name('api.admin.users.show');
        Route::get('/users/edit/{user}', 'UserController@edit')->name('api.admin.users.edit');
        Route::patch('/users/edit/{user}', 'UserController@update')->name('api.admin.users.update');
    });

});

