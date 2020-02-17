<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/8
 * Time: 11:11
 */
namespace app\sh\controller\api;
use EasyWeChat\Factory;
use think\Db;
use think\Exception;
use my\Sendsms;
class Api extends Common {

    public function index() {
        halt($this->cmd);
        return $this->fetch();
    }

    public function getVideo() {
        $list = [
//            [
//                'video_url' => 'static/sh/video/tcy02.mp4',
//                'poster' => 'static/sh/video/poster-tcy02.JPG'
//            ],
            [
                'video_url' => 'static/sh/video/001.mp4',
                'poster' => 'static/sh/video/poster-001.JPG'
            ],
//            [
//                'video_url' => 'static/sh/video/002.mp4',
//                'poster' => 'static/sh/video/poster-002.JPG'
//            ],
//            [
//                'video_url' => 'static/sh/video/003.mp4',
//                'poster' => 'static/sh/video/poster-003.JPG'
//            ],
            [
                'video_url' => 'static/sh/video/004.mp4',
                'poster' => 'static/sh/video/poster-004.JPG'
            ],
        ];
        return ajax($list);
    }
    //首页轮播图列表
    public function homeSlide() {
        $list = Db::table('mp_slideshow')->where('status',1)->order(['sort'=>'ASC'])->select();
        return ajax($list);
    }

    public function articleList() {
        $page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $where = [
            ['status','=',1]
        ];

        $count = Db::table('mp_article')->where($where)->count();
        $list = Db::table('mp_article')
            ->where($where)
            ->order(['create_time'=>'DESC'])
            ->field('id,title,desc,pic,tags')
            ->limit(($page-1)*$perpage,$perpage)->select();
        $tag = Db::table('mp_tag')->select();

        $tag_arr = $this->get_tag_arr($tag);

        foreach ($list as &$v) {
            $mytag = explode(',',$v['tags']);
            $arr = [];
            foreach ($mytag as $vv) {
                if(isset($tag_arr[$vv])) {
                    $arr[] = [
                        'tag_id' => $vv,
                        'tag_name' => $tag_arr[$vv]
                    ];
                }
            }
            $v['tag_list'] = $arr;
        }
        $data['count'] = $count;
        $data['totalPage'] = ceil($count/10);
        $data['list'] = $list;
        return ajax($data);
    }
    //首页资讯
    public function homeArticle() {
        $info = Db::table('mp_article')->order(['create_time'=>'DESC'])->find();
        $tag = Db::table('mp_tag')->select();
        $tag_arr = $this->get_tag_arr($tag);
        $mytag = explode(',',$info['tags']);
        $arr = [];
        foreach ($mytag as $v) {
            if(isset($tag_arr[$v])) {
                $arr[] = [
                    'tag_id' => $v,
                    'tag_name' => $tag_arr[$v]
                ];
            }
        }
        $info['tag_list'] = $arr;
        return ajax($info);
    }
    //获取资讯详情
    public function articleDetail() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $exist = Db::table('mp_article')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('',-4);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($exist);
    }
    //案例列表
    public function caseList() {
        $where = [
            ['status' ,'=', 1]
        ];
        try {
            $list = Db::table('mp_case')->where($where)->select();
        }catch (\Exception $e) {
            return ajax('SQL错误: ' . $e->getMessage(),-1);
        }
        return ajax($list);
    }
    //案例详情
    public function caseDetail() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $exist = Db::table('mp_case')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('',-4);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($exist);
    }
    //获取合作伙伴列表
    public function partnerList() {
        $where = [
            ['status' ,'=', 1]
        ];
        try {
            $list = Db::table('mp_partner')->where($where)->field('id,title,pic')->select();
        }catch (\Exception $e) {
            return ajax('SQL错误: ' . $e->getMessage(),-1);
        }
        return ajax($list);
    }
    //获取明星文物
    public function star_relic() {
        $data['id'] = 1;
        $data['title'] = '';
        $data['desc'] = '';
        $data['pic'] = 'static/sh/he.gif';
        return ajax($data);
    }
    //获取博物馆列表
    public function museumList() {
        try {
            $list = Db::table('mp_museum')->where('status',1)->field('id,title,pic')->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }
    //获取博物馆详情
    public function museumDetail() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $exist = Db::table('mp_museum')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('',-4);
            }
            $where = [
                ['mid','=',$val['id']],
                ['status','=',1]
            ];
            $data['detail'] = $exist;
            $data['slidelist'] = Db::table('mp_collection')->where($where)->field('id,pic,title')->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($data);
    }

    public function voiceDetail() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $exist = Db::table('mp_collection')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('',-4);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($exist);
    }
    //免费体验文案
    public function expNotice() {
        $str = '具体体验方式，我们会通过您留下的联系信息通知您，感谢您的访问！';
        return ajax($str);
    }
    //免费体验
    public function experience() {
        $val['realname'] = input('realname');
        $val['tel'] = input('tel');
        $val['email'] = input('email');
        $this->checkPost($val);
        if(!is_tel($val['tel'])) {
            return ajax('',2);
        }
        if(!is_email($val['email'])) {
            return ajax('',3);
        }
        $val['desc'] = input('desc');
        try {
            Db::table('mp_exp')->insert($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();

    }

    public function aboutUs() {
        $info = Db::table('mp_about')->where('id',1)->find();
        return ajax($info);
    }

    public function minipro() {
        $list = Db::table('mp_minipro')->select();
        return ajax($list);
    }
    //小程序登录
    public function login()
    {
        $code = input('post.code');
        $this->checkPost(['code'=>$code]);
        $app = Factory::miniProgram($this->mp_config);
        $info = $app->auth->session($code);
        if(isset($info['errcode']) && $info['errcode'] !== 0) {
            return ajax($info,-1);
        }
        $ret['openid'] = $info['openid'];
        $ret['session_key'] = $info['session_key'];
        try {
            $exist = Db::table('mp_user')->where('openid',$ret['openid'])->find();
            if($exist) {
                $uid = $exist['id'];
                Db::table('mp_user')->where('openid',$ret['openid'])->update(['last_login_time'=>time()]);
            }else {
                $insert = [
                    'create_time'=>time(),
                    'last_login_time'=>time(),
                    'openid'=>$ret['openid']
                ];
                Db::table('mp_user')->insert($insert);
                $uid = Db::table('mp_user')->getLastInsID();
            }
            $third_session = exec('/usr/bin/head -n 80 /dev/urandom | tr -dc A-Za-z0-9 | head -c 168');
            $token_exist = Db::table('mp_token')->where('uid',$uid)->find();
            //把3rd_session存入mysql
            if(!$token_exist) {
                Db::table('mp_token')->insert([
                    'token' => $third_session,
                    'uid' => $uid,
                    'value' => serialize($ret),
                    'end_time' =>time() + 3600*24*7
                ]);
            }else {
                Db::table('mp_token')->where('uid',$uid)->update([
                    'token' => $third_session,
                    'uid' => $uid,
                    'value' => serialize($ret),
                    'end_time' =>time() + 3600*24*7
                ]);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $json['token'] = $third_session;
        return ajax($json);
    }
    //小程序用户授权
    public function userAuth() {
        $iv = input('post.iv');
        $encryptData = input('post.encryptedData');
        $this->checkPost([
            'iv' => $iv,
            'encryptedData' => $encryptData
        ]);
        if(!$iv || !$encryptData) {
            return ajax([],-2);
        }
        $app = Factory::miniProgram($this->mp_config);
        try {
            $decryptedData = $app->encryptor->decryptData($this->myinfo['session_key'], $iv, $encryptData);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        try {
            $data['nickname'] = $decryptedData['nickName'];
            $data['avatar'] = $decryptedData['avatarUrl'];
            $data['sex'] = $decryptedData['gender'];
//            $data['unionid'] = $decryptedData['unionId'];
            $data['user_auth'] = 1;
            Db::table('mp_user')->where('openid','=',$decryptedData['openId'])->update($data);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax('授权成功',1);
    }
    //检测用户是否授权
    public function checkUserAuth() {
        $token = input('post.token');
        try {
            $exist = Db::table('mp_token')->where([
                ['token','=',$token],
                ['end_time','>',time()]
            ])->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if(!$exist) {
            return ajax('invalid token',-3);
        }
        $uid = $exist['uid'];
        try {
            $userauth = Db::table('mp_user')->where('id',$uid)->value('user_auth');
            if($userauth == 1) {
                return ajax(true);
            }else {
                return ajax(false);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }
    //小程序页面多张图
    public function getLongImg() {
        $array = [
            'static/sh/img/long1.gif',
            'static/sh/img/long2.gif',
            'static/sh/img/long3.gif',
            'static/sh/img/long4.gif',
            'static/sh/img/long5.gif',
            'static/sh/img/long6.gif',
            'static/sh/img/long7.jpg',
        ];
        return ajax($array);
    }




    //提交表单发送手机短信
    public function sendSms() {
        $val['tel'] = input('post.tel');
        checkPost($val);
        $sms = new Sendsms();
        $tel = $val['tel'];

        if(!is_tel($tel)) {
            return ajax('无效的手机号',2);
        }
        try {
            $code = mt_rand(100000,999999);
            $insert_data = [
                'tel' => $tel,
                'code' => $code,
                'create_time' => time()
            ];
            $sms_data['tel'] = $val['tel'];
            $sms_data['param'] = [
                'code' => $code
            ];
            $exist = Db::table('mp_verify')->where('tel','=',$tel)->find();
            if($exist) {
                if((time() - $exist['create_time']) < 60) {
                    return ajax('1分钟内不可重复发送',4);
                }
                $res = $sms->send($sms_data,'SMS_174925606');
                if($res->Code === 'OK') {
                    Db::table('mp_verify')->where('tel',$tel)->update($insert_data);
                    return ajax();
                }else {
                    return ajax($res->Message,-1);
                }
            }else {
                $res = $sms->send($sms_data);
                if($res->Code === 'OK') {
                    Db::table('mp_verify')->insert($insert_data);
                    return ajax();
                }else {
                    return ajax($res->Message,-1);
                }
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }

    //小程序购买套餐下单支付
    public function order() {
        $val['name'] = input('post.name');
        $val['tel'] = input('post.tel');
        $val['code'] = input('post.code');
        checkPost($val);
        $uid = $this->myinfo['uid'];
        if(!is_tel($val['tel'])) {
            return ajax('无效的手机号',2);
        }
        try {
            //    //todo 检验短信验证码
            $whereCode = [
                ['tel','=',$val['tel']],
                ['code','=',$val['code']]
            ];
            $code_exist = Db::table('mp_verify')->where($whereCode)->find();
            if($code_exist) {
                if((time() - $code_exist['create_time']) > 60*5) {
                    return ajax('验证码过期',5);
                }
            }else {
                return ajax('验证码无效',6);
            }

            $price = 19800;
            $order_sn = create_unique_number('');

            $insert_data = [
                'uid' => $uid,
                'order_sn' => $order_sn,
                'price' => $price,
                'name' => $val['name'],
                'tel' => $val['tel'],
                'create_time' => time(),
            ];

            Db::table('mp_vip_order')->insert($insert_data);

            $app = Factory::payment($this->mp_config);
            $total_price = $price;
            $result = $app->order->unify([
                'body' => '山海服务套餐',
                'out_trade_no' => $order_sn,
            'total_fee' => 1,
//                    'total_fee' => floatval($total_price)*100,
                'notify_url' => $this->weburl . 'api/pay/funding_notify',
                'trade_type' => 'JSAPI',
                'openid' => $this->myinfo['openid']
            ]);

            if($result['return_code'] == 'FAIL') {
                return ajax($result['return_msg'],-1);
            }else if($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
                return ajax($result['err_code_des'],-1);
            }

            $sign['appId'] = $result['appid'];
            $sign['timeStamp'] = strval(time());
            $sign['nonceStr'] = $result['nonce_str'];
            $sign['signType'] = 'MD5';
            $sign['package'] = 'prepay_id=' . $result['prepay_id'];
            $sign['paySign'] = getSign($sign);

        }catch (\Exception $e) {
            $this->log($this->cmd ,$e->getMessage());
            return ajax($e->getMessage(),-1);
        }

        return ajax($sign);

    }

    public function orderList() {
        try {
            $whereOrder = [
                ['uid','=',$this->myinfo['uid']]
            ];
            $orderby = ['id'=>'DESC'];
            $list = Db::table('mp_vip_order')->where($whereOrder)->order($orderby)->select();
        } catch(\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }














    public function uploadImage() {
        if(!empty($_FILES)) {
            if(count($_FILES) > 1) {
                return ajax('最多上传一张图片',9);
            }
            $info = $this->upload(array_keys($_FILES)[0]);
            if($info['error'] === 0) {
                return ajax(['path'=>$info['data']]);
            }else {
                return ajax($info['msg'],9);
            }
        }else {
            return ajax('请上传图片',30);
        }
    }


    private function get_tag_arr($arr) {
        $array = [];
        foreach ($arr as $v) {
            $array[$v['id']] = $v['tag_name'];
        }
        return $array;
    }


}