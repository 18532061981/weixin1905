<?php

namespace App\Admin\Controllers;

use App\Model\WxUserModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WxMsgController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '微信用户管理';


    public function sendMsg(){
        echo __METHOD__;
        $openid_arr = WxUserModel::select('openid','nickname','sex')->get()->toArray();
        echo '<pre>';print_r($openid_arr);echo '</pre>';
        $openid =array_column($openid_arr,'nickname');
        echo '<pre>';print_r($openid);echo '</pre>';

        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=28_ww-kHbJffMqOMBfKPW4Uk6nygqDi3EVc2zNvHN0XzIBbXq_fmlBQbI464f9GwlJijZPDhn4jtOeW39qQJtqqfUdpjGbdvyeZdVwH5Y7nwLZ78J4KTsLFYePpOc36_wm1xbuts9Xl6x_vcN_GSTKhABAHIZ
';
            $msg = date('Y-m-d H:i:s') . '哈哈哈哈哈哈哈哈哈哈或或';

        $data = [
            'touser'  => $openid,
            'msgtype' => 'text',
            'text'    => ['content'=>$msg]
        ];

        $client = new Client();

        $response = $client->request('POST',$url,[
            'body' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        echo $response->getBody();

    }


}
