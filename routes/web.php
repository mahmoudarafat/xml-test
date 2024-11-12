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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['as' => 'zatca.', 'prefix' => 'zatca','namespace' => 'App\Http\Controllers'], function () {

    Route::get('zatca-settings', 'ZatcaController@getZatcaSettings')->name('zatca-settings');
    Route::post('zatca-settings.update', 'ZatcaController@updateZatcaSetting')->name('zatca-settings.update');
    Route::post('zatca-submit', 'ZatcaController@zatcaSubmit')->name('zatca-submit');
    Route::post('/submit-invoice', 'ZatcaController@submitInvoice');

    Route::any('submit-invoice/{type}/{targetId}', 'ZatcaController@submitInvoice')->name('submit-invoice');
    Route::get('renew-certificate', 'ZatcaController@renewCertificate')->name('renew-certificate');
    Route::post('renew-certificate.store', 'ZatcaController@renewCertificateStore')->name('renew-certificate.store');

});

