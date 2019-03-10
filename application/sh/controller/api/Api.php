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

class Api extends Common {

    public function index() {
        halt($this->cmd);
        return $this->fetch();
    }
    //首页轮播图列表
    public function homeSlide() {
        $list = Db::table('mp_slideshow')->where('status',1)->order(['sort'=>'ASC'])->select();
        return ajax($list);
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
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($exist);
    }
    //
    public function museumSlide() {

    }
    //免费体验文案
    public function expNotice() {
        $str = '我们会通过您留下的联系方式通知您XXXXXXXXXXXXXXXXXXXXXXXX
        您在使用本服务过程中，可能可以为您使用的帐号设置昵称、头像、签名、留言等信息，也可能为您建立或者管理、参与的QQ群、微信群等设置名称、图片、简介等信息。您应当保证这些信息的内容和形式符合法律法规（本协议中的“法律法规”指用户所属/所处地区、国家现行有效的法律、行政法规、司法解释、地方法规、地方规章、部门规章及其他规范性文件以及对于该等法律法规的不时修改和补充，以及相关政策规定等，下同。）、公序良俗、社会公德以及腾讯平台规则，且不会侵害任何主体的合法权益。';
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

        $exist = Db::table('mp_user')->where('openid',$ret['openid'])->find();
        if($exist) {
            try {
                Db::table('mp_user')->where('openid',$ret['openid'])->update(['last_login_time'=>time()]);
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }
        }else {
            try {
                $insert = [
                    'create_time'=>time(),
                    'last_login_time'=>time(),
                    'openid'=>$ret['openid']
                ];
                Db::table('mp_user')->insert($insert);
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }
        }
        //把3rd_session存入redis
        $third_session = exec('/usr/bin/head -n 80 /dev/urandom | tr -dc A-Za-z0-9 | head -c 168');
        Db::table('mp_token')->insert([
            'token' => $third_session,
            'value' => serialize($ret),
            'end_time' =>time() + 3600*24*7
        ]);
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

        $exist = Db::table('mp_user')->where('openid','=',$decryptedData['openId'])->find();
        if($exist['avatar']) {
            return ajax('已授权',1);
        }

        try {
            $data['nickname'] = $decryptedData['nickName'];
            $data['avatar'] = $decryptedData['avatarUrl'];
            $data['sex'] = $decryptedData['gender'];
            Db::table('mp_user')->where('openid','=',$decryptedData['openId'])->update($data);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),4);
        }
        return ajax('授权成功',1);
    }

    //小程序入驻下单
    public function order() {
        $val['uid'] = input('post.uid');
        $val['linkman'] = input('post.linkman');
        $val['tel'] = input('post.tel');
        $val['email'] = input('post.email');
        $val['set'] = input('post.set');
        $this->checkPost($val);
        $val['cert'] = input('post.cert');


        if(!in_array($val['set'],[1,2,3])) {
            return ajax($val['set'],-3);
        }
        if(!is_tel($val['tel'])) {
            return ajax('',6);
        }
        if(!is_email($val['email'])) {
            return ajax('',7);
        }
        try {
            $user_exist = Db::table('hjmallind_user')->where('id',$val['uid'])->find();
            if(!$user_exist) {
                return ajax($val['uid'],-3);
            }
            $order_exist = Db::table('mp_order')->where('uid',$val['uid'])->find();
            if($order_exist) {
                if($order_exist['enter'] == 1) {
                    return ajax('',8);
                }
                $val['order_sn'] = $order_exist['order_sn'];
                $order_exist = true;
            }else {
                $order_exist = false;
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $cert = $val['cert'];
        if($cert) {
            if(!file_exists($cert)) {
                return ajax($cert,5);
            }
            $val['cert'] = $this->rename_file($cert);
        }else {
            unset($val['cert']);
        }

        $taocan = [
            '1'=>[
                'title' => '1元套餐',
                'price' => 1
            ],
            '2'=>[
                'title' => '2元套餐',
                'price' => 2
            ],
            '3'=>[
                'title' => '3元套餐',
                'price' => 3
            ]
        ];

        $val['title'] = $taocan[$val['set']]['title'];
        $val['price'] = $taocan[$val['set']]['price'];
        $val['openid'] = $user_exist['wechat_open_id'];
        try {
            if($order_exist) {
                $val['enter'] = 0;
                Db::table('mp_order')->where('uid',$val['uid'])->update($val);
            }else {
                $val['order_sn'] = create_unique_number('m');
                $val['create_time'] = time();
                Db::table('mp_order')->insert($val);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val);

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