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

Route::group(['prefix' => 'v1'], function(){
	Route::post('auth/register', 'AuthController@register')->name('auth.register');
	Route::post('auth/login', 'AuthController@login')->name('auth.login');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'v1'], function(){
	Route::get('auth/show', 'AuthController@show')->name('auth.show');
	Route::get('auth/logout', 'AuthController@logout')->name('auth.logout');
	Route::apiResource('clients', 'ClientController');
	Route::apiResource('products', 'ProductController');
    Route::apiResource('invoices', 'InvoiceController');
	Route::get('users', 'UserController@index')->name('users.index');
	Route::get('clients/{id}/invoices', 'InvoiceController@index')->name('clients.invoices.show');
	Route::get('search/clients', 'ClientController@searchClient')->name('clients.searchClient');
    Route::get('search/products', 'ProductController@searchProduct')->name('products.searchProduct');
    Route::get('invoices', 'InvoiceController@search')->name('invoices.search');

	Route::group(['prefix' => 'report'], function (){
        Route::get('clients', 'ReportController@clients')->name('report.clients');
        Route::get('calls', 'ReportController@calls')->name('report.calls');
        Route::get('bests', 'ReportController@bests')->name('report.bests');
        Route::get('worst', 'ReportController@worst')->name('report.worst');
        Route::get('deprecated', 'ReportController@deprecated')->name('report.deprecated');
        Route::get('{clientId}/totalPerYear', 'ReportController@totalPerYear')->name('report.totalPerYear');
        Route::get('totalInvoices', 'ReportController@totalInvoices')->name('report.totalInvoices');
    });

    Route::group(['prefix' => 'import'], function (){
        Route::post('clients', 'ImportController@clients')->name('import.clients');
        Route::post('invoices', 'ImportController@invoices')->name('import.invoices');
    });

});
