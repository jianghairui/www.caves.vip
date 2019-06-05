<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/4/8
 * Time: 17:09
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Test extends Controller {

    protected $config = [];
    protected $weburl = '';

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->cmd = request()->controller() . '/' . request()->action();
        $this->weburl = 'www.caves.vip';
        $this->config = [
            'appid' => 'wx1ef69d837c12a709',
            'app_secret' => 'b95d230bfbd56c0e1944e7f5d36b01f2',
            'mch_id' => '1251831001',
            'appkey' => '123654aaaZZZcaifu888aaazzzaaazzz',
            'sslcert_path' => '/mnt/www.caves.vip/public/cert/bwg_cert/apiclient_cert.pem',
            'sslkey_path' => '/mnt/www.caves.vip/public/cert/bwg_cert/apiclient_key.pem'
        ];
        die('YOU DIE');
    }
    //企业付款到用户(提现用)
    public function transfers() {
        $total_amount = 100;
        $re_openid = 'oNEu_s8TWzpK6p6-kUFnFHaS1GiI';
        $check_name = '姜海蕤';
        $desc = '山洞提现';

        $data=array(
            'mch_appid'=>$this->config['appid'],//商户账号appid
            'mchid'=> $this->config['mch_id'],//商户号
            'nonce_str'=>$this->randomkeys(32),//随机字符串
            'partner_trade_no'=> date('YmdHis').rand(1000, 9999),//商户订单号
            'openid'=> $re_openid,//用户openid
            'check_name'=>'NO_CHECK',//校验用户姓名选项,
            're_user_name'=> $check_name,//收款用户姓名
            'amount'=>$total_amount,//金额
            'desc'=> $desc,//企业付款描述信息
            'spbill_create_ip'=> '47.105.169.186',//Ip地址
        );
        $data['sign'] = $this->getSign($data);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers'; //调用接口
        $res = $this->curl_post_datas($url,$this->array2xml($data),true);
        halt($res);
    }

    public function notify() {
        //将返回的XML格式的参数转换成php数组格式
        $xml = file_get_contents('php://input');
        $data = $this->xml2array($xml);
        if($data) {
            $this->log('notify',var_export($data,true));
            if($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS') {
                $pay_order_sn = $data['out_trade_no'];
                $map = [
                    'pay_order_sn'=>$pay_order_sn,
                    'status' => 0
                ];

                try {
                    OrdersModel::where($map)->update(['trans_id'=>$data['transaction_id'],'status'=>1,'pay_time'=>time()]);
                    //TODO 异步发送模板消息
                    $this->asyn_sendPayNotifyTpl($data);
                }catch (\Exception $e) {
                    $this->excep('wx/notify:2',$e->getMessage());
                    exit($this->array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));
                }
            }else if($data['return_code'] == 'SUCCESS' && $data['result_code'] != 'SUCCESS'){
                try {
                    DB::table('pay_error')->insert(['title'=>'pay failed','content'=>json_encode($data)]);
                }catch (\Exception $e) {
                    $this->excep('wx/notify:3',$e->getMessage());
                }
            }

        }
        exit($this->array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));
    }

    public function pay() {
        $appid = $this->config['appid'];
        $secret = $this->config['app_secret'];
        $mch_id = $this->config['mch_id'];
        if(!isset($_GET['code'])){
            $redirect_uri=urlencode($_SERVER['REQUEST_SCHEME'] . "://".$_SERVER['HTTP_HOST']."/wx/testpay");
            $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
            header("Location:".$url);
            exit();
        }else {
            /*-------------获取OPENID开始-------------*/
            $code = $_GET["code"];
            $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $get_token_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $res = curl_exec($ch);
            curl_close($ch);
            $json_obj = json_decode($res, true);
            $openid = $json_obj['openid'];
            /*-------------获取OPENID结束-------------*/
            $arr = [
                'appid' => $appid,
                'mch_id' => $mch_id,
                'nonce_str' => $this->randomkeys(32),
                'sign_type' => 'MD5',
                'body' => '山洞-文创产品',
                'out_trade_no' => $this->genPayOrderSn(''),
                'total_fee' => 1,
                'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
                'notify_url' => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/wx/testnotify",
                'receipt' => 'Y',
                'trade_type' => 'JSAPI',
                'openid' => $openid
            ];
            $arr['sign'] = $this->getSign($arr);
            $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
            $result = $this->curl_post_datas($url, $this->array2xml($arr));
            /*--------------微信统一下单--------------*/
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                $result['timestamp'] = time();
                $arr2['appId'] = $arr['appid'];
                $arr2['timeStamp'] = $result['timestamp'];
                $arr2['nonceStr'] = $arr['nonce_str'];
                $arr2['signType'] = $arr['sign_type'];
                $arr2['package'] = 'prepay_id=' . $result['prepay_id'];
                $arr2['paySign'] = $this->getSign($arr2);
                return view('weixin/pay',['prepay'=>$arr2]);
            } else {
                exit('<script>alert("'.$result['return_msg'].'");document.addEventListener("WeixinJSBridgeReady", function(){ WeixinJSBridge.call("closeWindow"); }, false);</script>');
            }
        }
    }

    public function testnotify() {
        //将返回的XML格式的参数转换成php数组格式
        $xml = file_get_contents('php://input');
        $data = $this->xml2array($xml);
        if($data) {
            $this->log('wx/notify',var_export($data,true));
        }
        exit($this->array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));
    }












    private function log($cmd = '',$msg = '') {
        $file= 'notify.txt';
        $text='[Time ' . date('Y-m-d H:i:s') ."]  cmd:".$cmd."\n".$msg."\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

    private function excep($cmd = '',$msg = '') {
        $file= 'exception.txt';
        $text='[Time ' . date('Y-m-d H:i:s') ."]  cmd:".$cmd."\n".$msg."\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

//生成签名
    private function getSign($arr)
    {
        //去除数组中的空值
        $arr = array_filter($arr);
        //如果数组中有签名删除签名
        if(isset($arr['sing']))
        {
            unset($arr['sing']);
        }
        //按照键名字典排序
        ksort($arr);
        //生成URL格式的字符串
        $str = http_build_query($arr)."&key=" . $this->config['appkey'];
        $str = $this->arrToUrl($str);
        return  strtoupper(md5($str));
    }
    //URL解码为中文
    private function arrToUrl($str)
    {
        return urldecode($str);
    }

    private function curl_post_data($url, $curlPost)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, 1, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);
        return $data;
    }

    private function curl_post_datas($url, $curlPost,$userCert = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if($userCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $this->config['sslcert_path']);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $this->config['sslkey_path']);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, 1, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);
        $arr = $this->xml2array($data);
        return $arr;
    }


    private function array2xml($arr) {
        if(!is_array($arr) || count($arr) <= 0) {
            return false;
        }
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    private function xml2array($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    private function genPayOrderSn($letter = '') {
        $time = explode (" ", microtime ());
        $timeArr = explode('.',$time [0]);
        $mtime = array_pop($timeArr);
        $fulltime = $letter.$time[1] . $mtime . mt_rand(100,999);
        return $fulltime;
    }

    private function randomkeys($length) {
        $returnStr='';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for($i = 0; $i < $length; $i ++) {
            $returnStr .= $pattern {mt_rand ( 0, 61 )};
        }
        return $returnStr;
    }


}