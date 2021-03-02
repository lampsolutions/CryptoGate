<?php

Route::get('/request/{uuid}/',['uses' => 'Client\InvoiceRequestController@index'])->name('invoicerequest.index');
Route::post('/request/{uuid}/create',['uses' => 'Client\InvoiceRequestController@create'])->name('invoicerequest.create');

Route::get('/payments/{uuid}/',['uses' => 'Client\PaymentsController@select'])->name('payments.select');

Route::get('/payments/{uuid}/{currency}/paper/image/',['uses' => 'Client\PaymentsController@paper_image'])->name('payments.paper.image');
Route::get('/payments/{uuid}/{currency}/paper/',['uses' => 'Client\PaymentsController@paper_bip70_handler'])->name('payments.paper');

Route::get('/payments/{uuid}/checkPayment/{paymentId?}',['uses' => 'Client\PaymentsController@checkPayment'])->name('payments.check');
Route::get('/payments/{uuid}/{currency}/',['uses' => 'Client\PaymentsController@index'])->name('payments.pay');

Route::get('/',['uses' => 'DefaultController@index']);
Route::get('/datenschutz',['uses' => 'Client\LegalController@dsgvo'])->name('legal.privacy');
Route::get('/impressum',['uses' => 'Client\LegalController@impressum'])->name('legal.impressum');
Route::get('/agb',['uses' => 'Client\LegalController@agb'])->name('legal.agb');

Route::get('/branding/preview/select',['uses' => 'Branding\PreviewController@index']);
Route::post('/branding/preview/select',['uses' => 'Branding\PreviewController@index']);

Route::get('donate','DonateController@index')->name('Donate');
Route::post('donate/create','DonateController@create')->name('Donate:create');
Route::get('donate/thankyou','DonateController@thankYou')->name('Donate:thankYou');
Route::get('donate/cancel','DonateController@cancel')->name('Donate:cancel');
Route::get('donate/doi/{uuid}','DonateController@doi')->name('Donate:doi');
Route::get('donate/doubleOptIn/{uuid}','DonateController@doubleOptIn')->name('Donate:doubleOptIn');


Route::get('gift','GiftController@index')->name('Gift');
Route::post('gift/create','GiftController@create')->name('Gift:create');
Route::get('gift/thankyou','GiftController@thankYou')->name('Gift:thankYou');
Route::get('gift/cancel','GiftController@cancel')->name('Gift:cancel');


