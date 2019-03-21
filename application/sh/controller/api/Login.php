<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/20
 * Time: 16:36
 */
namespace app\sh\controller\api;
use EasyWeChat\Factory;
use think\Db;
use think\Exception;

class Login extends Common {
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
                $uid = Db::table('mp_user')->insert($insert);
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

        $exist = Db::table('mp_user')->where('openid','=',$decryptedData['openId'])->find();
        if($exist['avatar']) {
            return ajax('已授权',1);
        }

        try {
            $data['nickname'] = $decryptedData['nickName'];
            $data['avatar'] = $decryptedData['avatarUrl'];
            $data['sex'] = $decryptedData['gender'];
            $data['unionid'] = $decryptedData['unionId'];
            $data['user_auth'] = 1;
            Db::table('mp_user')->where('openid','=',$decryptedData['openId'])->update($data);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),4);
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


}