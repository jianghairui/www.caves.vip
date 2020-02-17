<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/4
 * Time: 10:50
 */
namespace app\sh\controller\api;
use EasyWeChat\Factory;
use think\Db;
use think\Exception;

class Pay extends Common {

    //订单支付回调接口
    public function order_notify() {
        //将返回的XML格式的参数转换成php数组格式
        $xml = file_get_contents('php://input');

        $data = xml2array($xml);
        if($data) {
            $this->paylog($this->cmd,var_export($data,true));

            if($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS') {
                $whereOrder = [
                    ['order_sn','=',$data['out_trade_no']],
                    ['status','=',0]
                ];
                try {
                    $order_exist = Db::table('mp_vip_order')->where($whereOrder)->find();
                    if($order_exist) {
                        $update_data = [
                            'trans_id' => $data['transaction_id'],
                            'status' => 1,
                            'pay_time' => time()
                        ];
                        //修改订单状态,众筹金额增加
                        Db::table('mp_vip_order')->where($whereOrder)->update($update_data);
                    }
                }catch (\Exception $e) {
                    $this->log($this->cmd,$e->getMessage());
                }
            }

        }
        exit(array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));

    }






}