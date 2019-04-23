<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/4/23
 * Time: 11:55
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Fake extends Controller {
/*
 * 1.user 表添加数据
 *
 *
 * */
    public function index() {
        $dir =  'fake/fact/2';
//        $insert_data = [];
        $files = scandir($dir);
        unset($files[0]);unset($files[1]);
//        foreach ($files as $v) {
//            $data['sex'] = mt_rand(1,2);
//            $data['age'] = mt_rand(35,55);
//            $data['avatar'] = $dir .'/'. $v;
//            $data['fake'] = 1;
//            $data['role'] = 4;
//            $data['user_auth'] = 1;
//            $data['auth'] = 2;
//            $data['create_time'] = time();
//            $insert_data[] = $data;
//        }
//        try {
//            $res = Db::table('mp_user')->insertAll($insert_data);
//        } catch (\Exception $e) {
//            die($e->getMessage());
//        }
//        halt($res);
//        $res($insert_data);
//        try {
//            $where = [
//                ['fake','=',1],
//                ['id','>=',187],
//                ['role','>=',4]
//            ];
//            $ids = Db::table('mp_user')->where($where)->column('id');
//            $insert_role = [];
//            foreach ($ids as $k=>$v) {
//                $data['role'] = 4;
//                $data['fake'] = 1;
//                $data['uid'] = $v;
//                $data['cover'] = $dir . '/' . $files[$k+2];
//                $insert_role[] = $data;
//            }
//            $res = Db::table('mp_role')->insertAll($insert_role);
//        } catch (\Exception $e) {
//            return ajax($e->getMessage(), -1);
//        }
//        halt($res);


    }

    public function test() {

    }

}