<?php

Route::group(['middleware' => ['web']], function () {

    // Home
    Route::get('/', [
        'uses' => 'HomeController@index',
        'as' => 'home'
    ]);


    // Service
    Route::get('service/order', ['uses' => 'ServiceController@indexOrder', 'as' => 'service.order', 'middleware' => 'admin']);
    Route::get('services', 'ServiceController@indexFront');
    Route::get('service/create', ['uses' => 'ServiceController@create',
        'middleware' => 'admin',
        'as' => 'service.create'
    ]);

    //PAYMENT
    Route::get('service/payment/{service_id}', ['uses' => 'PaymentController@createPayment', 'middleware' => 'manager']);
    Route::get('service/executePayment', ['uses' => 'PaymentController@executePayment', 'middleware' => 'manager']);
    Route::post('ipnListen', ['uses' => 'PaymentController@ipnListener']);

    //manage services
    Route::post('service/create', ['uses' => 'ServiceController@store', 'middleware' => 'admin']);
    Route::post('service/destroy/{service_id}', ['uses' => 'ServiceController@destroy', 'middleware' => ['permit', 'admin']]);
    Route::post('service/config/{service_id}', ['uses' => 'ServiceController@config', 'middleware' => ['permit', 'admin']]);
    Route::get('service/edit/{service_id}', ['uses' => 'ServiceController@edit', 'middleware' => ['permit', 'admin']]);
    Route::put('service/update/{service_id}', ['uses' => 'ServiceController@update', 'as' => 'service.update', 'middleware' => ['permit', 'admin']]);




    //perform phpexcel
    Route::get('service/run/{service_id}', ['uses' => 'ExcelController@calculate', 'middleware' => 'permit']);
    Route::post('service/run/{service_id}', ['uses' => 'ExcelController@calculate', 'middleware' => 'permit']);

    //ajax
    Route::put('postactive/{id}', 'ServiceController@updateActive', ['middleware' => ['permit', 'admin']]);
    Route::post('postrelation/{user_id}', 'ServiceController@relation', ['middleware' => ['permit', 'admin']]);


    // Comment
    Route::resource('comment', 'CommentController');

    Route::put('commentseen/{id}', 'CommentController@updateSeen');
    Route::put('uservalid/{id}', 'CommentController@valid');


    // Contact
    Route::resource('contact', 'ContactController', [
        'middleware' => ['director']
    ]);


    // User
    Route::get('user/create', ['uses' => 'StaffController@create', 'middleware' => 'manager', 'as' => 'user.create']);
    Route::post('user/create', ['uses' => 'StaffController@store', 'middleware' => 'manager']);
    Route::get('user/show', ['uses' => 'StaffController@index', 'middleware' => 'manager', 'as' => 'user.show']);
    Route::get('user/destroy/{staff_id}', ['uses' => 'StaffController@destroyStaff', 'middleware' => 'manager', 'as' => 'user.destroy']);


//	Route::resource('user', 'StaffController');
    // Authentication routes...
    Route::get('auth/login', 'UserAuthController@getLogin');
    Route::post('auth/login', 'UserAuthController@postLogin');
    Route::get('auth/logout', 'UserAuthController@getLogout');
    Route::get('auth/confirm/{token}', 'UserAuthController@getConfirm');


    // Resend routes...
    Route::get('auth/resend', 'UserAuthController@getResend');

    // Registration routes...
    Route::get('auth/register', 'UserAuthController@getRegister');
    Route::post('auth/register', 'UserAuthController@postRegister');

    // Password reset link request routes...
    Route::get('password/email', 'UserAuthController@getEmail');
    Route::post('password/email', 'UserAuthController@postEmail');

    // Password reset routes...
    Route::get('password/reset/{token}', 'UserAuthController@getReset');
    Route::post('password/reset', 'UserAuthController@postReset');
});
