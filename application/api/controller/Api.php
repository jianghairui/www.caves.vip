<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/8
 * Time: 11:11
 */
namespace app\api\controller;
use think\Db;
use think\Exception;

class Api extends Common {

    public function index() {
        return $this->fetch();
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
            return ajax('请上传图片',3);
        }
    }

    private function sortMerge($node,$pid=0)
    {
        $arr = array();
        foreach($node as $key=>$v)
        {
            if($v['pid'] == $pid)
            {
                $v['child'] = $this->sortMerge($node,$v['id']);
                $arr[] = $v;
            }
        }
        return $arr;
    }

    private function recursion($array,$to_cid=0) {
        $to_array = [];
        foreach ($array as $v) {
            if($v['root_cid'] == $to_cid) {
                $v['child'] = $this->recursion($array,$v['id']);
                $to_array[] = $v;
            }
        }
        return $to_array;
    }

}