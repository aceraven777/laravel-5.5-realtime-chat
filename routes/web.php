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

// Route::get('event', function() {
// 	$user = new App\User([
// 		'name' => 'Yamite',
// 		'email' => 'yamite@gmail.com',
// 		'password' => bcrypt('123456'),
// 	]);

// 	$user->save();

// 	event(new App\Events\UserRegistered($user));

// 	return 'Done';
// });

Route::group(['middleware' => ['auth']], function () {
	Route::get('/', 'HomeController@index')->name('home');

	Route::get('users', 'UserController@index')->name('users.index');
	Route::get('users/{to_user_id}/messages', 'MessageController@chatMessages')->name('users.chat-messages');
	Route::post('users/{to_user_id}/messages', 'MessageController@chatUser')->name('users.chat-user');
});