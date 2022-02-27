<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
 
Route::get('installation/{code}/{domain}/{email}', 'SmApiController@checkDomainOrPurchaseCodeExist');


Route::get('verify/{product_code}/{domain}', 'SmApiController@verifyProductCode');
Route::get('verified/email/{product_code}/{email}', 'SmApiController@verifiedEmailStore');

