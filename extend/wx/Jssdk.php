<?php
namespace  wx;
class Jssdk { //微信官网的jssdk
    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $filename = "jsapi_ticket.php";
        if(file_exists($filename)) {
            $data = json_decode($this->get_php_file($filename));
            if ($data->expire_time < time()) {
                $accessToken = $this->getAccessToken();
                $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
                $res = json_decode($this->httpGet($url));
                $ticket = $res->ticket;
                if ($ticket) {
                    $data->expire_time = time() + 7000;
                    $data->jsapi_ticket = $ticket;
                    $this->set_php_file($filename, json_encode($data));
                }
            } else {
                $ticket = $data->jsapi_ticket;
            }
        }else {
            $accessToken = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data = new \stdClass;
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $this->set_php_file($filename, json_encode($data));
            }
        }
        return $ticket;
    }

    public function getCardTicket() {
        $filename = "wxcard_ticket.php";
        if(file_exists($filename)) {
            $data = json_decode($this->get_php_file($filename));
            if ($data->expire_time < time()) {
                $accessToken = $this->getAccessToken();
                $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=wx_card&access_token=$accessToken";
                $res = json_decode($this->httpGet($url));
                $ticket = $res->ticket;
                if ($ticket) {
                    $data->expire_time = time() + 7000;
                    $data->wxcard_ticket = $ticket;
                    $this->set_php_file($filename, json_encode($data));
                }
            } else {
                $ticket = $data->wxcard_ticket;
            }
        }else {
            $accessToken = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=wx_card&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data = new \stdClass;
                $data->expire_time = time() + 7000;
                $data->wxcard_ticket = $ticket;
                $this->set_php_file($filename, json_encode($data));
            }
        }
        return $ticket;
    }

    public function getAccessToken() {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $filename = "access_token.php";
        if(file_exists($filename)) {
            $data = json_decode($this->get_php_file($filename));
            if ($data->expire_time < time()) {
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
                $res = json_decode($this->httpGet($url));
                $access_token = $res->access_token;
                if ($access_token) {
                    $data->expire_time = time() + 7000;
                    $data->access_token = $access_token;
                    $this->set_php_file($filename, json_encode($data));
                }
            } else {
                $access_token = $data->access_token;
            }
        }else {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data = new \stdClass;
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $this->set_php_file($filename, json_encode($data));
            }
        }
        return $access_token;
    }

    private function httpGet($url) {//https不是必需的
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//同时关闭这2项
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//同时关闭这2项即可跳过https验证
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    private function get_php_file($filename) {
        return trim(substr(file_get_contents($filename), 0));
    }

    private function set_php_file($filename, $content) {
        $fp = fopen($filename, "w");
        fwrite($fp, $content);
        fclose($fp);
/*        "<?php exit();?>" . */
    }
}


?>