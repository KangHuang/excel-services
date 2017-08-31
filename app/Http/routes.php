<?php

Route::group(['middleware' => ['web']], function () {

	// Home
	Route::get('/', [
		'uses' => 'HomeController@index', 
		'as' => 'home'
	]);


	// Service
	Route::get('service/order', ['uses' => 'ServiceController@indexOrder', 'as' => 'service.order','middleware' => 'admin']);
	Route::get('services', 'ServiceController@indexFront');
	Route::get('service/search', 'ServiceController@search');
        Route::get('service/create', ['uses'=> 'ServiceController@create',
            'middleware' => 'admin',
            'as' => 'service.create'
            ]);
        
        //PAYMENT
        Route::get('service/payment/{service_id}', ['uses'=> 'PaymentController@createPayment','middleware' => 'manager']);
        Route::get('service/executePayment', ['uses'=> 'PaymentController@executePayment','middleware' => 'manager']);
        Route::post('ipnListen',['uses'=> 'PaymentController@ipnListener']);

        //manage services
        Route::post('service/create', ['uses'=> 'ServiceController@store','middleware' => 'admin']);  
        Route::post('service/destroy/{service_id}', ['uses'=> 'ServiceController@destroy','middleware' => 'permit']);  
        Route::post('service/config/{service_id}', ['uses'=> 'ServiceController@config','middleware' => 'permit']);  
        
        
        //perform phpexcel
	Route::get('service/run/{service_id}', ['uses' => 'ExcelController@calculate','middleware' => 'permit']);
        Route::post('service/run/{service_id}', ['uses' => 'ExcelController@calculate','middleware' => 'permit']);
        
        //ajax
	Route::put('postseen/{id}', 'ServiceController@updateSeen');
	Route::put('postactive/{id}', 'ServiceController@updateActive');
	Route::post('postrelation/{user_id}', 'ServiceController@relation');        


	// Comment
	Route::resource('comment', 'CommentController');

	Route::put('commentseen/{id}', 'CommentController@updateSeen');
	Route::put('uservalid/{id}', 'CommentController@valid');


	// Contact
	Route::resource('contact', 'ContactController', [
		'middleware' => ['director']
	]);


	// User
	Route::get('user/create', ['uses' => 'UserController@create','middleware' => 'manager', 'as'=>'user.create']);
	Route::post('user/create', ['uses' => 'UserController@store','middleware' => 'manager']);
        Route::get('user/show', ['uses' => 'UserController@index','middleware' => 'manager', 'as'=>'user.show']);
        Route::get('user/destroy/{staff_id}', ['uses' => 'UserController@destroyStaff','middleware' => 'manager', 'as'=>'user.destroy']);


//	Route::resource('user', 'UserController');

	// Authentication routes...
	Route::get('auth/login', 'Auth\AuthController@getLogin');
	Route::post('auth/login', 'Auth\AuthController@postLogin');
	Route::get('auth/logout', 'Auth\AuthController@getLogout');
	Route::get('auth/confirm/{token}', 'Auth\AuthController@getConfirm');
        

	// Resend routes...
	Route::get('auth/resend', 'Auth\AuthController@getResend');

	// Registration routes...
	Route::get('auth/register', 'Auth\AuthController@getRegister');
	Route::post('auth/register', 'Auth\AuthController@postRegister');

	// Password reset link request routes...
	Route::get('password/email', 'Auth\PasswordController@getEmail');
	Route::post('password/email', 'Auth\PasswordController@postEmail');

	// Password reset routes...
	Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
	Route::post('password/reset', 'Auth\PasswordController@postReset');
        
     
});