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

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
	Route::get('/', 'HomeController@index')->name('home');

	Route::get('users', 'UserController@index')->name('users.index');
	Route::get('users/{to_user}/messages', 'MessageController@index')->name('users.chat-messages');
	
	Route::get('api/messages/{toUser}', 'Api\MessageController@index')->name('api.users.chat-messages');
	Route::post('api/users/{toUser}/messages', 'Api\MessageController@chatUser')->name('api.users.chat-user');
});