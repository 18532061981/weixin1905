<?php

namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WeixinController extends Controller
{
    //
    /*微信*/
    public function wechat()
    {
        $token='2259b56f5898cd6192c50';
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $echostr=$_GET['echostr'];

        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );



        if($tmpStr == $signature){
            echo $echostr;
        }else{
            die('not ok');
        }
    }

    /**
     * 接收微信推送事件
     */


    public function receic(){
        $log_file = "wx.log";
    //将接收的数据记录到日志文件
        $data =data('Y-m-d H:i:s') . json_encode($_POST);
        file_put_contents($log_file,$data,FILE_APPEND);

    }




    /**
     * 获取用户基本信息
     */

    public function getUserInfo(){

        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN';

    }




}
