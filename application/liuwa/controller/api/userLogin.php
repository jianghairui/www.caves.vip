<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/20
 * Time: 16:36
 */
namespace app\index\controller\api;
use app\liuwa\controller\api\Base;
use EasyWeChat\Factory;
use think\Db;
use think\Exception;

class userLogin extends Base {

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
            $insert = [
                'create_time'=>time(),
                'last_login_time'=>time(),
                'openid'=>$ret['openid'],
            ];
            try {
                Db::table('mp_user')->insert($insert);
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }
        }
        return ajax();
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
            $data['gender'] = $decryptedData['gender'];
            $data['city'] = $decryptedData['city'];
            $data['country'] = $decryptedData['country'];
            $data['province'] = $decryptedData['province'];
            Db::table('mp_user')->where('openid','=',$decryptedData['openId'])->update($data);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),4);
        }
        return ajax('授权成功',1);
    }

    public function getPhoneNumber() {

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
            return ajax($e->getMessage(),24);
        }

        try {
            $data['tel'] = $decryptedData['phoneNumber'];
            Db::table('mp_user')->where('openid','=',$this->myinfo['openid'])->update($data);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),24);
        }
        return ajax($decryptedData,1);
    }


}