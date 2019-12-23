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

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/','Index\IndexController@index');  //网站首页展示




Route::get('/text/xml','Text\TextController@xmlText');


/*微信开发*/
Route::get('/wx','Wx\WeixinController@wechat');
Route::post('/wx','Wx\WeixinController@receiv');
Route::get('/wx/flush/access_token','Wx\WeixinController@flushAccessToken');        //刷新access_token
Route::get('/wx/menu','Wx\WeixinController@createMenu');        //创建菜单
Route::get('/wx/qrcode','Wx\WxQRController@qrcode');        //创建有参数的临时二维码


/**
 * 微信投票
 */

Route::get('/vote','VoteController@index');        //微信投票


/**
 * 详情页展示
 */
Route::prefix('single')->group(function(){
    Route::get('single','Single\SingleController@single');   //详情页展示

});