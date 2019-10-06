<?php

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

Route::get('/','TransactionController@index')->middleware('auth')->name('transactions.index');
Route::get('/transactions/create','TransactionController@create')->middleware('auth')->name('transactions.create');
Route::post('/transactions','TransactionController@store')->middleware('auth')->name('transactions.store');
Route::get('/users','UserAutocompleteController@index')->middleware('auth')->name('auto-users.index');


// Authentication Routes...
Route::get('login/', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login/', 'Auth\LoginController@login');
Route::post('logout/', 'Auth\LoginController@logout')->name('logout');

// Register Routes...
Route::get('regiser/', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('regiser/', 'Auth\RegisterController@register');

Route::group([
    'prefix' => 'admin',
    'namespace' => 'Admin',
    'middleware' => ['auth','admin']
],function(){
    Route::get('/transactions','TransactionController@index')->name('admin.transactions.index');
    Route::get('/transactions/{id}','TransactionController@show')->name('admin.transactions.show');
    Route::patch('/transactions/edit/{id}','TransactionController@update')->name('admin.transactions.update');


    Route::get('/users','UserController@index')->name('admin.users.index');
    Route::get('/users/{user}','UserController@show')->name('admin.users.show');
    Route::get('/users/edit/{user}','UserController@edit')->name('admin.users.edit');
    Route::patch('/users/edit/{user}','UserController@update')->name('admin.users.update');

});
