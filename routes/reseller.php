<?php
/**
 * Created by PhpStorm.
 * User: mrlog
 * Date: 11.08.2018
 * Time: 14:26
 */

Route::get('/', 'DashboardController@index')->name('reseller.dashboard');

Route::get('undoLogin', 'DashboardController@undoLogin')->name('reseller.undoLogin');

Route::prefix('accounting')->namespace('Accounting')->group(function() {
    Route::get('/', 'AccountingController@index')->name('reseller.accounting.index');
    Route::post('/', 'AccountingController@add')->name('reseller.accounting.add');

    Route::prefix('{year}/{month}')->group(function() {
        Route::get('/', 'AccountingController@single')->name('reseller.accounting.single.index');
        Route::get('/export', 'AccountingController@export')->name('reseller.accounting.single.export');
    });

    Route::prefix('payment')->group(function() {
        Route::prefix('paypal')->group(function() {
            Route::get("confirm", "PaypalController@getSuccess")->name("reseller.payment.paypal.success");
            Route::get("error", "PaypalController@getError")->name("reseller.payment.paypal.error");
        });

        Route::prefix('paysafecard')->group(function() {
            Route::get("success", "PaysafecardController@getSuccess")->name("reseller.payment.paysafecard.success");
            Route::get("error", "PaysafecardController@getError")->name("reseller.payment.paysafecard.cancel");
            Route::any("notify", "PaysafecardController@anyNotify")->name("reseller.payment.paysafecard.notify");
        });
    });

});

Route::prefix('api')->namespace('Api')->group(function() {
    Route::get('/', 'ApiController@index')->name('reseller.api.index');
    Route::prefix('{api}')->group(function() {
        Route::get('/', 'ApiController@show')->name('reseller.api.single.index');
        Route::get('/refresh', 'ApiController@refresh')->name('reseller.api.single.refresh');
        Route::prefix('logs')->group(function() {
            Route::get('/', 'ApiController@logs')->name('reseller.api.single.logs.index');
        });
        Route::prefix('whitelist')->group(function() {
            Route::get('/', 'ApiController@whitelist')->name('reseller.api.single.whitelist.index');
            Route::post('/add', 'ApiController@addAddress')->name('reseller.api.single.whitelist.add');
            Route::post('/remove/{whitelist}', 'ApiController@removeAddress')->name('reseller.api.single.whitelist.remove');
        });
    });
});

Route::prefix('webspaces')->namespace('Webspace')->group(function() {
    Route::get('/', 'WebspaceController@index')->name('reseller.webspaces.index');
    Route::prefix('{webspace}')->group(function() {
        Route::get('/', 'WebspaceController@show')->name('reseller.webspace.single.index');
        Route::get('/autologin', 'WebspaceController@automaticLogin')->name('reseller.webspace.single.automaticLogin');
    });
});

Route::prefix('webspace')->namespace('Webspace')->group(function() {
    Route::get('/create', 'WebspaceController@create')->name('reseller.webspace.create');
    Route::post('/step1', 'WebspaceController@step1')->name('reseller.webspace.step1');
    Route::post('/step2', 'WebspaceController@step2')->name('reseller.webspace.step2');
});

Route::prefix('orders')->namespace('Orders')->group(function() {
    Route::get('/', 'OrdersController@index')->name('reseller.orders.index');
});

Route::prefix('service')->namespace('Service')->group(function() {
    Route::prefix('{service}')->group(function() {
        Route::get('extends', 'ExtendController@index')->name('reseller.service.extends');
        Route::post('extend', 'ExtendController@extend')->name('reseller.service.extend');
        Route::get('reconfigure', 'ReconfigureController@index')->name('reseller.service.reconfigure');
        Route::post('reconfigure', 'ReconfigureController@reconfigure')->name('reseller.service.reconfigure');
    });
});