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

    public function slideList() {
        try {
            $list = Db::table('mp_slideshow')->where([
                ['status','=',1]
            ])->order(['sort'=>'ASC'])->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }

    public function getReqList() {

    }

    public function getReqDetail() {

    }

//    public function




    public function uploadImage() {
        if(!empty($_FILES)) {
            if(count($_FILES) > 1) {
                return ajax('最多上传一张图片',9);
            }
            $path = $this->upload(array_keys($_FILES)[0]);
            return ajax(['path'=>$path]);
        }else {
            return ajax('请上传图片',3);
        }
    }

    public function uploadImage2m() {
        if(!empty($_FILES)) {
            if(count($_FILES) > 1) {
                return ajax('最多上传一张图片',9);
            }
            $path = $this->upload(array_keys($_FILES)[0],2048);
            return ajax(['path'=>$path]);
        }else {
            return ajax('请上传图片',3);
        }
    }


}