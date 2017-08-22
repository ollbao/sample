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

Route::get('/','StaticPagesController@home')->name('home');
Route::get('/help','StaticPagesController@help')->name('help');
Route::get('/about','StaticPagesController@about')->name('about');

Route::get('/signup','UsersController@create')->name('signup');
Route::get('/users/{user}', 'UsersController@show')->name('users.show');//单个用户页面
Route::get('/users/create', 'UsersController@create')->name('users.create');//创建页面
Route::post('/users/store', 'UsersController@store')->name('users.store');//创建页面提交

