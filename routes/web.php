<?php

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
    return redirect('home');
});

Auth::routes();

Route::get('home', 'HomeController@index')->name('home')->middleware('auth');
Route::get('ruang', 'RuangController@index')->name('ruangIndex')->middleware('auth');
Route::get('jenis', 'JenisController@index')->name('jenisIndex')->middleware('auth');
Route::get('inventaris', 'InventarisController@index')->name('inventarisIndex')->middleware('auth');
Route::get('peminjaman', 'PeminjamanController@index')->name('peminjamanIndex')->middleware('auth');
Route::post('peminjaman/add', 'PeminjamanController@add')->name('peminjamanAdd')->middleware('auth');