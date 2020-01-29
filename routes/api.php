<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('{endpoint}', 'Controller@api');

/**
 * API: Payment [PAYPAL, PAYSAFECARD, SOFORT];
 */
Route::namespace('applications')->group(function() {
    Route::prefix('payment')->namespace('payment')->group(function() {
        /**
         * Check transaction;
         */
        Route::namespace('check')->group(function() {
            /**
             * PayPal
             */
            Route::prefix('paypal')->group(function () {
                Route::get('success', 'PayPalCheck@getSuccess')->name('api.payment.paypal.success');
                Route::get('error', 'PayPalCheck@getError')->name('api.payment.paypal.error');
            });
            /**
             * PaySafeCard
             */
            Route::prefix('paysafecard')->group(function () {
                Route::get('success', 'PaySafeCardCheck@getSuccess')->name('api.payment.paysafecard.success');
                Route::get('error', 'PaySafeCardCheck@getError')->name('api.payment.paysafecard.error');
                Route::any('notify', 'PaySafeCardCheck@getNotify')->name('api.payment.paysafecard.notify');
            });
            /**
             * Sofort
             * Coming soon
             */
        });
    });
});