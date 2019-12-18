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

Route::get('/text/xml','Text\TextController@xmlText');


/*微信开发*/
Route::get('/wx','Wx\WeixinController@wechat');
Route::post('/wx','Wx\WeixinController@receiv');
Route::get('/wx/flush/access_token','Wx\WeixinController@flushAccessToken');        //刷新access_token
Route::get('/wx/menu','Wx\WeixinController@createMenu');        //创建菜单


/**
 * 微信投票
 */

Route::get('/vote','VoteController@index');        //微信投票