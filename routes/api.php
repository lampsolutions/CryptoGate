<?php
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
Route::post('/shopware/create',['uses' => 'Api\ShopwareController@create'])->name('shopware.create');
Route::post('/shopware/verify',['uses' => 'Api\ShopwareController@verify'])->name('shopware.verify');
Route::get('/shopware/supported_currencies',['uses' => 'Api\ShopwareController@supported_currencies'])
    ->name('shopware.supported_currencies');
Route::get('/shopware/list',['uses' => 'Api\ShopwareController@list'])
    ->name('shopware.list');


Route::post('/woocommerce/create',['uses' => 'Api\WoocommerceController@create'])->name('woocommerce.create');
Route::post('/woocommerce/verify',['uses' => 'Api\WoocommerceController@verify'])->name('woocommerce.verify');
Route::get('/woocommerce/supported_currencies',['uses' => 'Api\WoocommerceController@supported_currencies'])
    ->name('woocommerce.supported_currencies');
Route::get('/woocommerce/list',['uses' => 'Api\WoocommerceController@list'])
    ->name('shopware.list');

Route::post('/donateform/create',['uses' => 'Api\DonateFormController@create'])->name('donateform.create');
Route::post('/donateform/verify',['uses' => 'Api\DonateFormController@verify'])->name('donateform.verify');
Route::get('/donateform/list',['uses' => 'Api\DonateFormController@list'])
    ->name('donateform.list');

Route::post('/paymentform/create',['uses' => 'Api\PaymentFormController@create'])->name('PaymentForm.create');



