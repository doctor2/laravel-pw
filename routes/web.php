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

Route::get('/','TransactionController@index');
Route::get('/transactions/create','TransactionController@create');
Route::post('/transactions','TransactionController@store')->name('transactions.store');
Route::get('/users','Api\UserController@index')->name('users.index');


// Authentication Routes...
Route::get('login/', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login/', 'Auth\LoginController@login');
Route::post('logout/', 'Auth\LoginController@logout')->name('logout');

// Register Routes...
Route::get('regiser/', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('regiser/', 'Auth\RegisterController@register');