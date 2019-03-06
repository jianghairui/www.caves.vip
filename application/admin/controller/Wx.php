<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/1/30
 * Time: 14:15
 */
namespace  app\admin\controller;
use think\Db;
class Wx extends Common {

    public function test() {
//        halt($_SERVER);
        $order_sn = '155123918151385700317';
        $transid = '4200000249201902271111233536';
        $total_fee = 10;
        $arr = [
            'appid' => $this->config['appid'],
            'mch_id'=> $this->config['mch_id'],
            'nonce_str'=>$this->randomkeys(32),
            'sign_type'=>'MD5',
            'transaction_id'=> $transid,
            'out_trade_no'=> $order_sn,
            'out_refund_no'=> time(),
            'total_fee'=> floatval($total_fee)*100,
            'refund_fee'=> floatval($total_fee)*100,
            'refund_fee_type'=> 'CNY',
            'refund_desc'=> '订单退款',
            'notify_url'=> $_SERVER['REQUEST_SCHEME'] . '://'.$_SERVER['HTTP_HOST'].'/wxRefundNotify',
        ];
        $arr['sign'] = $this->getSign($arr);

        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $res = $this->curl_post_datas($url,$this->array2xml($arr),true);
        halt($res);
    }
    //订单退款
    public function refund() {
        $order_sn = input('post.order_sn');
        $map = [
            ['order_sn','=',$order_sn],
            ['status','in',[1,3,4]]
        ];
        try {
            $exist = Db::table('orders')->where($map)->find();
            if(!$exist) {
                return ajax('订单不存在或状态已改变',-1);
            }
            $total_fee = Db::table('orders')->where('pay_order_sn',$exist['pay_order_sn'])->sum('total_price');
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        $arr = [
            'appid' => $this->config['appid'],
            'mch_id'=> $this->config['mch_id'],
            'nonce_str'=>$this->randomkeys(32),
            'sign_type'=>'MD5',
            'transaction_id'=> $exist['trans_id'],
            'out_trade_no'=> $exist['pay_order_sn'],
            'out_refund_no'=> $exist['order_sn'],
            'total_fee'=> floatval($total_fee)*100,
            'refund_fee'=> floatval($exist['total_price'])*100,
            'refund_fee_type'=> 'CNY',
            'refund_desc'=> '订单退款',
            'notify_url'=> $_SERVER['REQUEST_SCHEME'] . '://'.$_SERVER['HTTP_HOST'].'/wxRefundNotify',
            'refund_account' => 'REFUND_SOURCE_UNSETTLED_FUNDS'
        ];
        $arr['sign'] = $this->getSign($arr);

        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $res = $this->curl_post_datas($url,$this->array2xml($arr),true);
        if($res && $res['return_code'] == 'SUCCESS') {
            if($res['result_code'] == 'SUCCESS') {
                try {//修改订单状态
                    $update_data = [
                        'status' => -1,
                        'refund_apply' => 2,
                        'refund_time' => time()
                    ];
                    Db::table('orders')->where($map)->update($update_data);
                }catch (\Exception $e) {
                    return ajax($e->getMessage(),-1);
                }
                return ajax();
            }else {
                return ajax($res['err_code_des'],-1);
            }
        }else {
            return ajax('退款通知失败',-1);
        }

    }

    public function refundNotify() {
        //将返回的XML格式的参数转换成php数组格式
        $xml = file_get_contents('php://input');
        $data = $this->xml2array($xml);
        if($data) {
            $this->refundLog($this->cmd,var_export($data,true));
        }
        exit($this->array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));
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

    private function genOrderSn($letter = '') {
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

    protected function refundLog($cmd = '',$msg = '') {
        $file= 'wxRefundNotify.txt';
        $text='[Time ' . date('Y-m-d H:i:s') ."]  cmd:".$cmd."\n".$msg."\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
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

    private function xmllog($cmd = '',$msg = '') {
        $file= 'xml.txt';
        $text='[Time ' . date('Y-m-d H:i:s') ."]  cmd:".$cmd."\n".$msg."\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

}