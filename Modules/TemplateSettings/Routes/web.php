<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('templatesettings')->group(function() {
    Route::get('/', 'TemplateSettingsController@index');
    Route::get('email-template', 'TemplateSettingsController@emailTemplate');
    Route::post('email-template', 'TemplateSettingsController@emailTemplateStore');
});
