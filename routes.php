<?php 

Route::group(['namespace' => 'App\Modules\Event\Http\Controllers', 'middleware' => ['web']], function () {

	// Events
	Route::resource('events', 'EventController');
	Route::post('event/register', 'RegistrationController@store');

});

Route::group(['namespace'=>'App\Modules\Event\Http\Controllers\Admin', 'middleware' => ['web', 'auth'], 'prefix' => 'admin'], function () {

	Route::get('events/registrations', 'EventController@showRegistrations');
	Route::resource('events', 'EventController');
	

});