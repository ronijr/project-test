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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(
    [
        'prefix'    => 'pajak',
        'namespace' => 'Api'
    ], function(){
    Route::post('/',[
        'uses' => 'PajakController@list',
        'name' => 'pajak.list'
    ]);
    Route::post('detail',[
        'uses' => 'PajakController@detail',
        'name' => 'pajak.detail'
    ]);
    Route::post('add',[
        'uses' => 'PajakController@created',
        'name' => 'pajak.add'
    ]);
    Route::post('edit',[
        'uses'  => 'PajakController@updated',
        'name'  => 'pajak.edit'
    ]);
    Route::post('delete',[
        'uses'   => 'PajakController@deleted',
        'name'   => 'pajak.delete',
    ]);
});


Route::group(
    [
        'prefix'    => 'items',
        'namespace' => 'Api'
    ], function(){
    Route::post('list',[
        'uses' => 'ItemsController@list',
        'name' => 'items.list'
    ]);
    Route::post('detail',[
        'uses' => 'ItemsController@detail',
        'name' => 'items.detail'
    ]);
    Route::post('add',[
        'uses' => 'ItemsController@created',
        'name' => 'items.add'
    ]);
    Route::post('edit',[
        'uses'  => 'ItemsController@updated',
        'name'  => 'items.edit'
    ]);
    Route::post('delete',[
        'uses'   => 'ItemsController@deleted',
        'name'   => 'items.delete',
    ]);
});
