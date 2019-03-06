<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/27
 * Time: 11:24
 */
namespace app\admin\controller;
use think\Db;

class Login extends Common {

    public function index() {
        if(session('username')) {
            $this->redirect('Index/index');
            exit();
        }
        $cookie = cookie('password');
        if(isset($cookie) && $cookie != '') {
            $data['username'] = cookie('username');
            $data['password'] = cookie('password');
            $data['remember_pwd'] = 1;
        }else {
            $data['username'] = '';
            $data['password'] = '';
            $data['remember_pwd'] = 0;
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function login() {
        if(request()->isPost()) {
            $login_vcode = input('post.login_vcode');
            if(strtolower($login_vcode) !== strtolower(session('login_vcode'))) {
                $this->error('验证码错误',url('Login/index'));
            }
            $where = [
                ['mobile','=',input('post.username')],
                ['role','=',0],
                ['password','=',md5(input('post.password') . config('login_key'))]
            ];
            $result = Db::table('user')->where($where)->find();
            if($result) {
                session('login_vcode',null);
                session('user_id',$result['id']);
                session('nickname',$result['nickname']);
                session('username',input('post.username'));

                if(input('post.remember_pwd') == 1) {
                    cookie('username',input('post.username'),3600*24*7);
                    cookie('password',input('post.password'),3600*24*7);
                }else {
                    cookie('username',null);
                    cookie('password',null);
                }
                $this->redirect(url('Index/index'));
            }else {
                $this->error('用户名密码不匹配',url('Login/index'));
            }
        }
    }

    public function logout() {
        session(null);
        $this->redirect('Login/index');
    }

    public function vcode() {
        $vcode = generateVerify(200,50,2,4,24);
        session('login_vcode',$vcode);
    }

    public function personal() {
        $id = session('user_id');
        $info = Db::table('user')->where('id','=',$id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function modifyInfo() {
        $id = session('user_id');
        $val['name'] = input('post.name');
        $val['sex'] = input('post.sex');
        $val['nickname'] = input('post.nickname');
        $this->checkPost($val);
        $val['password'] = input('post.password');
        if($val['password']) {
            $val['password'] = md5($val['password'] . config('login_key'));
        }else {
            unset($val['password']);
        }
        try {
            Db::table('user')->where('id','=',$id)->update($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val,1);
    }

    public function test() {
//        halt(session('login_vcode'));
//        session_start();
//        halt($_SESSION);
    }



















//    public function sets() {
//        session('myname','jianghairui');
//        session('age','27');
//        cookie('cookie_name','viki',30);
//        cookie('cookie_sex','0',30);
//
//    }
//
//    public function gets() {
//        dump(session('myname'));
//        dump(session('age'));
//        dump(cookie('cookie_name'));
//        dump(cookie('cookie_sex'));
//    }



}