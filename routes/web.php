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
Route::get('/egpaf/sync', ['uses' => 'EGPAFSyncController@index', 'as' => 'egpaf_sync']);
Route::get('/egpaf/sync_app', ['uses' => 'EGPAFSyncController@syncAppointments', 'as' => 'egpaf_sync_app']);
Route::get('/egpaf/sync_msg', ['uses' => 'EGPAFSyncController@syncClientOutgoing', 'as' => 'egpaf_sync_msg']);
Route::get('/nascop/sync', ['uses' => 'NascopController@syncClients', 'as' => 'nascop_sync']);
Route::get('/nascop/sync_app', ['uses' => 'NascopController@syncAppointments', 'as' => 'nascop_sync_app']);
