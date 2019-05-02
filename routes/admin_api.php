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
Route::get('/balance',['uses' => 'AdminApi\ElectrumController@balance'])->name('admin.api.electrum.balance');
Route::get('/listaddresses',['uses' => 'AdminApi\ElectrumController@listaddresses'])->name('admin.api.electrum.listaddresses');
Route::get('/listtransactions',['uses' => 'AdminApi\TransactionsController@listtransactions'])->name('admin.api.electrum.listtransactions');

Route::get('/listrequests',['uses' => 'AdminApi\InvoiceRequestsController@list'])->name('admin.api.invoice.requests.list');
Route::get('/createrequest',['uses' => 'AdminApi\InvoiceRequestsController@create'])->name('admin.api.invoice.requests.create');
Route::get('/getrequest',['uses' => 'AdminApi\InvoiceRequestsController@get'])->name('admin.api.invoice.requests.get');
Route::get('/updaterequest',['uses' => 'AdminApi\InvoiceRequestsController@update'])->name('admin.api.invoice.requests.update');
Route::get('/deleterequest',['uses' => 'AdminApi\InvoiceRequestsController@delete'])->name('admin.api.invoice.requests.delete');

Route::post('/setconfig',['uses' => 'AdminApi\ConfigController@set'])->name('admin.api.config.set');

