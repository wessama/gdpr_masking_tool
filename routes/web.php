<?php

Route::redirect('/', '/login');
Route::redirect('/home', '/admin');
Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Requests
    Route::post('requests/retry', 'RequestsController@retry')->name('requests.retry');
    Route::delete('requests/destroy', 'RequestsController@massDestroy')->name('requests.massDestroy');
    Route::get('requests/create/single', 'RequestsController@create_alt')->name('requests.createAlt');
    Route::resource('requests', 'RequestsController');

    // Logs
    Route::delete('logs/destroy', 'RequestLogController@massDestroy')->name('logs.massDestroy');
    Route::resource('logs', 'RequestLogController');

});
