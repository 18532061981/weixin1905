<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VoteController extends Controller
{
    //微信投票
    public function index(){

        echo '<pre>';print_r($_GET);echo '</pre>';
        $code = $_GET['code'];

        //获取access_token
        $this->getAccessToken($code);
    }

    /**
     * 根据code获取access_token
     */
    protected function getAccessToken($code){

        $url = ' https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'&code='.$code.'&grant_type=authorization_code';
        $json_data = file_get_contents($url);
        $data = $json_data($json_data,true);
        echo '<pre>';print_r($_POST);echo '</pre>';
    }

}
