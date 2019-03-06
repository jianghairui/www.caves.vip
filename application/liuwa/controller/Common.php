<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/25
 * Time: 16:12
 */
namespace app\liuwa\controller;
use my\Auth;
use think\Db;
use think\Controller;
use think\exception\HttpResponseException;

class Common extends Controller {

    protected $mp_config = [];
    protected $weburl = '';
    protected $cmd;

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->cmd = request()->controller() . '/' . request()->action();
        $this->weburl = 'www.caves.vip';

        if(!$this->needSession()) {
            if(request()->isPost()) {
                throw new HttpResponseException(ajax([],-2));
            }else {
                $this->error('请登录后操作',url('Login/index'));
            }
        }


    }

    private function needSession() {
        $noNeedSession = [
            'Login/index',
            'Login/vcode',
            'Login/login',
            'Login/test',
        ];
        if (in_array(request()->controller() . '/' . request()->action(), $noNeedSession)) {
            return true;
        }else {
            if(session('username') && session('mploginstatus') && session('mploginstatus') == md5(session('username') . 'jiang')) {
                return true;
            }else {
                return false;
            }
        }
    }

    protected function upload($k) {
        if($this->checkfile($k) !== true) {
            return array('error'=>1,'msg'=>$this->checkfile($k));
        }

        $filename_array = explode('.',$_FILES[$k]['name']);
        $ext = array_pop($filename_array);

        $path =  'static/liuwa/upload/' . date('Y-m-d');
        is_dir($path) or mkdir($path,0755,true);
        //转移临时文件
        $newname = create_unique_number() . '.' . $ext;
        move_uploaded_file($_FILES[$k]["tmp_name"], $path . "/" . $newname);
        $filepath = $path . "/" . $newname;

        return array('error'=>0,'data'=>$filepath);
    }

    //检验格式大小
    private function checkfile($file) {
        $allowType = array(
            "image/gif",
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/pjpeg",
            "image/bmp"
        );
        if($_FILES[$file]["type"] == '') {
            return '图片存在中文名或超过2M';
        }
        if(!in_array($_FILES[$file]["type"],$allowType)) {
            return '图片格式无效';
        }
        if($_FILES[$file]["size"] > 1024*512) {
            return '图片大小不超过300Kb';
        }
        if ($_FILES[$file]["error"] > 0) {
            return "error: " . $_FILES[$file]["error"];
        }else {
            return true;
        }
    }

    protected function checkPost($postArray) {
        if(empty($postArray)) {
            throw new HttpResponseException(ajax('数据不能为空',-3));
        }
        foreach ($postArray as $value) {
            if (is_null($value) || $value === '') {
                throw new HttpResponseException(ajax('数据不能为空',-3));
            }
        }
        return true;
    }

    protected function getip() {
        $unknown = 'unknown';
        if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        /*
        处理多层代理的情况
        或者使用正则方式：$ip = preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;
        */
        if (false !== strpos($ip, ','))
            $ip = reset(explode(',', $ip));
        return $ip;
    }



}