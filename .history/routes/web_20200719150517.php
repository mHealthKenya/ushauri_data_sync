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
    return view('welcome');
});

Route::get('/sync', ['uses' => 'SyncController@index', 'as' => 'sync']);
Route::get('/sync_app', ['uses' => 'SyncController@syncAppointments', 'as' => 'sync_app']);
Route::get('/sync_msg', ['uses' => 'SyncController@syncClientOutgoing', 'as' => 'sync_msg']);
