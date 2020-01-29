<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 11.08.2018
 * Time: 14:03
 */

Route::get('/', 'DashboardController@index')->name('admin.dashboard');

Route::prefix('resellers')->namespace('Resellers')->group(function() {
    Route::get('/', 'ResellersController@index')->name('admin.resellers.index');
    Route::post('/', 'ResellersController@create')->name('admin.reseller.create');
    Route::prefix('{user}')->group(function() {
        Route::get('/', 'ResellersController@show')->name('admin.resellers.single.index');
        Route::post('/edit', 'ResellersController@edit')->name('admin.reseller.edit');
        Route::post('/moneyedit', 'ResellersController@editMoney')->name('admin.reseller.editmoney');
        Route::get('/login', 'ResellersController@login')->name('admin.resellers.single.login');
    });
});

Route::prefix('api')->namespace('API')->group(function() {
    Route::get('/', 'ApiController@index')->name('admin.api.index');
    Route::post('/create', 'ApiController@create')->name('admin.api.create');
    Route::prefix('{api}')->group(function() {
        Route::get('/', 'ApiController@show')->name('admin.api.single.index');
        Route::post('/', 'ApiController@optionsadd')->name('admin.api.single.optionsadd');
        Route::post('{option}', 'ApiController@remove')->name('admin.api.single.remove');

    });
});

Route::prefix('apioptions')->namespace('APIOptions')->group(function() {
    Route::get('/', 'ApiOptionController@index')->name('admin.apioptions.index');
    Route::post('/create', 'ApiOptionController@create')->name('admin.apioption.create');
    Route::prefix('{apioption}')->group(function() {
        Route::get('/toggleState', 'ApiOptionController@toggleState')->name('admin.apioption.toggleState');
        Route::get('/destroy', 'ApiOptionController@destroy')->name('admin.apioption.destroy');
    });
});

Route::prefix('transactions')->namespace('Transactions')->group(function() {
    Route::get('/', 'TransactionsController@index')->name('admin.transactions.index');
});

Route::prefix('apilogs')->namespace('APILogs')->group(function() {
    Route::get('/', 'ApiLogsController@index')->name('admin.apilogs.index');
});