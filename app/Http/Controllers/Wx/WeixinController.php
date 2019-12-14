<?php

namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WxUserModel;

class WeixinController extends Controller
{

    protected $access_token;

    public function __construct()
    {
        //获取access_token
        $this->access_token = $this->getAccessToken();

    }

    protected function getAccessToken(){

        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET');
        $data_json=file_get_contents($url);
        $arr = json_decode($data_json,true);
        [
            'access_token' => 'xxxx',
            'expires' => 7200
        ];
        return $arr['access_token'];
    }


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


    public function receiv(){
        $log_file = "wx.log";
    //将接收的数据记录到日志文件
        $xml_str = file_get_contents("php://input");
        $data =date('Y-m-d H:i:s') . $xml_str;
        file_put_contents($log_file,$data,FILE_APPEND);
        //处理xml数据
        $xml_obj = simplexml_load_string($xml_str);

        //入库 其他逻辑
         $event = $xml_obj->Event ;//获取事件类型
            if($event=='subscribe') {
                $openid = $xml_obj->FromUserName;  //获取用户的opendi
                $u = WxUserModel::where(['openid'=>$openid])->first();
                if($u){
                    $msg = '欢迎回来';
                    $xml = '<xml>
  <ToUserName><![CDATA['.$openid.']]></ToUserName>
  <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
  <CreateTime>'.time().'</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA['.$msg.']]></Content>
</xml>';
                    echo $xml;
                }else{
                    //获取用户信息 zcza
                    $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid.'&lang=zh_CN';
                    $user_info = file_get_contents($url);       //
                    $u = json_decode($user_info,true);
                    $user_data = [
                        'openid'   => $openid,
                        'subscribe_time' => $xml_obj->CreateTime,  //关注时间
                    ];
                    //openid 入库
                    $uid = WxUserModel::insertGetId($user_data);
                    var_dump($uid);
                    die;
                }


                //获取用户信息
                $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid.'&lang=zh_CN';
                $user_info = file_get_contents($url);
                file_put_contents('wx_user.log', $user_info, FILE_APPEND);
            }
        //判断消息类型
        $msg_type = $xml_obj->MsgType;

        $touser = $xml_obj->FromUserName;   //接收消息的用户oppenid
        $fromuser = $xml_obj->ToUserName;    //开发者公众号的ID;
        $time = time();



        if($msg_type=='text'){
            $content = date('Y-m-d H:i:s') . $xml_obj->Content;
            $response_text='<xml>
  <ToUserName><![CDATA['.$touser.']]></ToUserName>
  <FromUserName><![CDATA['.$fromuser.']]></FromUserName>
  <CreateTime>'.$time.'</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA['.$content.']]></Content>
</xml>';
            echo  $response_text;    //回复用户消息
        }
    }




    /**
     * 获取用户基本信息
     */

    public function getUserInfo($access_token,$openid){

        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'openid='.$openid.'&lang=zh_CN';
        //发送网络请求
        $json_str = file_get_contents($url);
        $log_file = 'wx_user.log';
        file_put_contents($log_file,$json_str,FILE_APPEND);
    }




}
