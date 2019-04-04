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
    return redirect('ruang');
});

Auth::routes();

Route::get('home', function () {
    return redirect('peminjaman');
})->name('home')->middleware('auth');
Route::get('ruang', 'RuangController@index')->name('ruangindex')->middleware('auth');
Route::get('jenis', 'JenisController@index')->name('jenisindex')->middleware('auth');
Route::get('inventaris', 'InventarisController@index')->name('inventarisindex')->middleware('auth');
Route::get('pegawai', 'PegawaiController@index')->name('pegawaiindex')->middleware('auth');
Route::get('peminjaman', 'PeminjamanController@index')->name('peminjamanindex')->middleware('auth');
Route::get('peminjaman/new', 'PeminjamanController@new')->name('peminjamanindex')->middleware('auth');
Route::post('peminjaman/add', 'PeminjamanController@add')->name('peminjamanAdd')->middleware('auth');
Route::post('peminjaman/return', 'PeminjamanController@returnInventaris')->name('peminjamanReturn')->middleware('auth');
