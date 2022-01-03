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
Route::post('/login', 'AuthController@login')->name('login');
Route::post('/register', 'AuthController@register');
Route::post('/send-reset-email', 'APIController@sendResetEmail');

Route::group(['middleware' => ['auth:api']], function () {
	// GET
	Route::get('/me', 'APIController@getMe');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth:api']], function() {
	Route::get('/sell-offers', 'AdminController@getSellOffersList');
	Route::get('/buy-offers', 'AdminController@getBuyOffersList');
	Route::get('/export', 'AdminController@exportCSV');

	Route::group(['prefix' => 'batches'], function() {

		Route::get('/', 'AdminController@getBatchesList');		
		Route::get('/{id}', 'AdminController@getBatchDetail');
		Route::get('/{id}/sell-offers', 'AdminController@getBatchSellOffersList');
		Route::get('/{id}/buy-offers', 'AdminController@getBatchBuyOffersList');
		Route::get('/{id}/export', 'AdminController@detailExportCSV');
		
		Route::post('/', 'AdminController@createBatch');
		Route::put('/{id}', 'AdminController@updateBatch');

		Route::delete('/{id}', 'AdminController@removeBatch');
	
	});
	
});


Route::group(['prefix' => 'user', 'middleware' => ['auth:api']], function() {

	Route::group(['prefix' => 'sell-offers', 'middleware' => ['auth:api']], function() {
		Route::get('/', 'SellerController@getOffersList');
		Route::post('/', 'SellerController@createOffer');
		Route::delete('/{id}', 'SellerController@removeOffer');
	});

	Route::group(['prefix' => 'buy-offers', 'middleware' => ['auth:api']], function() {
		Route::get('/', 'BuyerController@getOffersList');
		Route::post('/', 'BuyerController@createOffer');
		Route::put('/{id}', 'BuyerController@updateOffer');
		Route::delete('/{id}', 'BuyerController@removeOffer');
	});

});

