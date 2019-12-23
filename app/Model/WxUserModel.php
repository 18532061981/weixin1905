<?php

namespace App\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;

class WxUserModel extends Model
{
    //
    protected $table = 'wx_users';
    protected $primaryKey = 'uid';

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


}
