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

Route::get('/','StaticPagesController@home')->name('home');
Route::get('/help','StaticPagesController@help')->name('help');
Route::get('/about','StaticPagesController@about')->name('about');

//注册
Route::get('signup','UsersController@create')->name('signup');
Route::resource('users','UsersController');

//session
Route::get('login','SessionsController@create')->name('login');
Route::post('login','SessionsController@store')->name('login');
Route::delete('logout','SessionsController@destroy')->name('logout');

Route::get('signup/confirm/{token}','UsersController@confirmEmail')->name('confirm_email');


Route::get('password/reset','PasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email','PasswordController@sendResetLinkEmail')->name('password.email');

Route::get('password/reset/{token}','PasswordController@showResetForm')->name('password.reset');
Route::post('password/reset','PasswordController@reset')->name('password.update');

Route::resource('statuses','StatusesController',['only'=>['store','destroy']]);


Route::get('/users/{user}/followings','UsersController@followings')->name('users.followings');
Route::get('/users/{user}/followers','UsersController@followers')->name('users.followers');


Route::post('/users/followers/{user}','FollowersController@store')->name('followers.store');
Route::delete('/users/follwers/{user}','FollowersController@destroy')->name('followers.destroy');
