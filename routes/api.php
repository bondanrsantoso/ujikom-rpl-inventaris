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
// Ruangan API Routes
Route::middleware('auth:api')->post('ruang/add', "RuangController@add");
Route::middleware('auth:api')->delete('ruang/delete', "RuangController@delete");
Route::get('ruang/get', "RuangController@get");
// Jenis API Routes
Route::middleware('auth:api')->post('jenis/add', "JenisController@add");
Route::middleware('auth:api')->delete('jenis/delete', "JenisController@delete");
Route::get('jenis/get', "JenisController@get");
// Inventaris API Routes
Route::middleware('auth:api')->post('inventaris/add', "InventarisController@add");
Route::middleware('auth:api')->delete('inventaris/delete', "InventarisController@delete");
Route::get('inventaris/get', "InventarisController@get");
// Peminjaman API Routes
Route::post('peminjaman/checkItem', "PeminjamanController@checkAvailableStock");
