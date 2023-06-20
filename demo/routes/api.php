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

Route::post('informations', ['uses' => 'App\Http\Controllers\InformationController@saveInfosTable']);
Route::post('informations/download', ['uses' => 'App\Http\Controllers\InformationController@download']);
Route::post('informations/upload', ['uses' => 'App\Http\Controllers\InformationController@upload']);
Route::post('informations/document', ['uses' => 'App\Http\Controllers\InformationController@setForm']);
Route::post('informations/pdf', ['uses' => 'App\Http\Controllers\InformationController@readPdf']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
