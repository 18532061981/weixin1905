<?php

namespace App\Http\Controllers\Text;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TextController extends Controller
{
    //
    public function xmlText(){
        $xml_str ='<xml><ToUserName><![CDATA[gh_510c51288372]]></ToUserName>
<FromUserName><![CDATA[oauvlvmY9LYPZcO2LkMnipIMtQ3M]]></FromUserName>
<CreateTime>1576149963</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[哈哈哈]]></Content>
<MsgId>22565044062118076</MsgId>
</xml>';
    $xml_obj = simplexml_load_string($xml_str);
        echo '<pre>';print_r($xml_obj);echo '</pre>';die;
        echo '<pre>';print_r($xml_obj);echo'</pre>'; echo '<hr>';
        echo 'ToUserName:'.$xml_obj->ToUserName; echo '<br>';
        echo 'FromUserName:'.$xml_obj->ToUserName; echo '<br>' ;

    }


}
