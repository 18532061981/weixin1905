<?php

namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\WxUserModel;
use GuzzleHttp\Client;

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
        $data =date('Y-m-d H:i:s') . ">>>>>>\n" . $xml_str . "\n\n";
        file_put_contents($log_file,$data,FILE_APPEND);
        //处理xml数据
        $xml_obj = simplexml_load_string($xml_str);

        //入库 其他逻辑
         $event = $xml_obj->Event ;//获取事件类型
        $openid = $xml_obj->FromUserName;  //获取用户的opendi
            if($event=='subscribe') {
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
                    #获取用户信息 zcza
                    $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid.'&lang=zh_CN';
                    $user_info = file_get_contents($url);       //
                    $u = json_decode($user_info,true);
                    $user_data = [
                        'openid'    => $openid,
                        'nickname'  => $u['nickname'],
                        'sex'       => $u['sex'],
                        'headimgurl'    => $u['headimgurl'],
                        'subscribe_time'    => $u['subscribe_time']
                    ];
                    #openid 入库
                    $uid = WxUserModel::insertGetId($user_data);
                    $msg = "谢谢关注";
                    #回复用户关注
                    $xml = '<xml>
  <ToUserName><![CDATA['.$openid.']]></ToUserName>
  <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
  <CreateTime>'.time().'</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA['.$msg.']]></Content>
</xml>';
                    echo $xml;
                }


                //获取用户信息
                $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid.'&lang=zh_CN';
                $user_info = file_get_contents($url);
                file_put_contents('wx_user.log', $user_info, FILE_APPEND);
            }elseif($event=='CLICK'){           // 菜单点击事件
                if($xml_obj->EventKey=='weather'){
                    //如果是 获取天气
                    //请求第三方接口 获取天气
                    $weather_api = 'https://free-api.heweather.net/s6/weather/now?location=beijing&key=5b6aff3a4deb4bd6aa1fbd0c48f1e05f';
                    $weather_info = file_get_contents($weather_api);
                    $weather_info_arr = json_decode($weather_info,true);
                    $cond_txt = $weather_info_arr['HeWeather6'][0]['now']['cond_txt'];
                    $tmp = $weather_info_arr['HeWeather6'][0]['now']['tmp'];
                    $wind_dir = $weather_info_arr['HeWeather6'][0]['now']['wind_dir'];
                    $msg = $cond_txt . ' 温度： '.$tmp . ' 风向： '. $wind_dir;
                    $response_xml = '<xml>
  <ToUserName><![CDATA['.$openid.']]></ToUserName>
  <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
  <CreateTime>'.time().'</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA['. date('Y-m-d H:i:s') .  $msg .']]></Content>
</xml>';
                    echo $response_xml;
                }
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

    /**
     * 刷新 access_token
     */
    public function flushAccessToken()
    {
        $key = 'wx_access_token';
        Redis::del($key);
        echo $this->getAccessToken();
    }

    /**
     * 创建自定义菜单
     */
    public function createMenu()
    {
        $url = 'http://es1905.qxywzc.cn/vote';
        $redirect_uri = urlencode($url);        //授权后跳转页面

        //创建自定义菜单的接口地址
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token;
        $menu = [
            'button'    => [
                [
                    'type'  => 'click',
                    'name'  => '获取天气',
                    'key'   => 'weather'
                ],[
                    'type'  => 'view',
                    'name' => '投票',
                    'url' => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa4148d6e658baa85&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo#wechat_redirect '
                ]
            ]
        ];
        $menu_json = json_encode($menu,JSON_UNESCAPED_UNICODE);
        $client = new Client();
        $response = $client->request('POST',$url,[
            'body'  => $menu_json
        ]);
        echo '<pre>';print_r($menu);echo '</pre>';
        echo $response->getBody();      //接收 微信接口的响应数据
    }




}
