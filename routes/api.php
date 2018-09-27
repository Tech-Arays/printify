<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register the API routes for your application as
| the routes are automatically authenticated using the API guard and
| loaded automatically by this application's RouteServiceProvider.
|
*/

Route::group([
    'middleware' => 'auth:api'
], function () {
    //
});

/**
 * Webhooks - Allowed for anonymous access
 */
Route::group(['middleware' => ['webhooks']], function () {
    // app webhooks
        Route::post('/dashboard/store/webhook', 'Dashboard\StoreController@webhook');

    // product webhooks
        Route::post('/dashboard/products/webhook', 'Dashboard\ProductsController@webhook');

    // order webhooks
        Route::post('/dashboard/orders/webhook', 'Dashboard\OrdersController@webhook');

    // braintree webhooks
        Route::post('/braintree-webhooks', 'BraintreeWebhookController@webhook');
});