<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/4/8
 * Time: 17:09
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Test extends Controller {

//    public function test() {
//        try {
//            $list = Db::table('mp_goods')->select();
//        } catch (\Exception $e) {
//            die($e->getMessage());
//        }
//
//        foreach ($list as $v) {
//            $id = $v['id'];
//            $pic = unserialize($v['pics'])[0];
//            $info = getimagesize($pic);
//            if($info) {
//                $data['width'] = $info[0];
//                $data['height'] = $info[1];
//            }else {
//                $data['width'] = 1;
//                $data['height'] = 1;
//            }
//            Db::table('mp_goods')->where('id',$id)->update($data);
//        }
//        echo 'SUCCESS';
//
//    }



}