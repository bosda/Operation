<?php
namespace App\Utils;

class Util{
    public function http_get($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $content = curl_exec($ch);
        curl_close($ch);
        return json_decode($content,true);
    }

    public function http_post($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        if(is_array($data)){
            $data = json_encode($data);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $content = curl_exec($ch);
        curl_close($ch);
        return json_decode($content,true);
    }

    /* 微信服务器验证 */
    public function wechat_check(){
        $token = 'operation';

        $signature = $request->input('signature');
        $timestamp = $request->input('timestamp');
        $nonce = $request->input('nonce');

        $arr = [$timestamp,$token,$nonce];
        sort($arr,SORT_STRING);
        $str = implode($arr);
        $str = sha1($str);
        if(!empty($signature) && $signature == $str){
            return $request->input('echostr');
        }else{
            return 0;
        }
    }

    public function get_access_token(){
        $appid = "wx891e583280d97390";
        $secret = "513070f18c47a8a0c223d140bab423da";
        $file = public_path().'\access_token.txt';
        if(is_file($file)){
            $access_token = file_get_contents($file);
            $time = substr($access_token, 0,10);
            if(($time+7200)>time()){
                return substr($access_token,11);
            }
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
        $access_token = $this->http_get($url)['access_token'];
        file_put_contents($file, time().'-'.$access_token);
        return $access_token;
    }

    /* 获取微信二维码 */
    public function get_wechat_ticket($user_id){
        $access_token = $this->get_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
        $data = [
            'expire_seconds' => 604800,
            'action_name' => 'QR_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_id' => $user_id
                ] 
            ]
        ];
        return $this->http_post($url, $data);
    }

    /* 发送文本消息 */
    public function send_msg($to,$form,$content){
        $xml = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        return sprintf($xml,$to,$form,time(),$content);
    }

    /* 发送模板消息 */
    public function send_tpl($openid, $template_id, $data){
        $access_token = $this->get_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";
        $arr = [
            'touser' => $openid,
            'template_id' => $template_id,
            'data' => $data
        ];
        return $this->http_post($url,$arr);
    }
}