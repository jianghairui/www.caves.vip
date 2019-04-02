<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/20
 * Time: 15:45
 */
namespace app\api\controller;

use think\Db;

class Shop extends Common {

    public function topCate() {
        try {
            $map = [
                ['del','=',0],
                ['status','=',1],
                ['pid','=',0]
            ];
            $list = Db::table('mp_goods_cate')->where($map)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }

    public function goodsList() {
        $pcate_id = input('post.pcate_id',0);
        $where = [
            ['status','=',1],
            ['del','=',0]
        ];
        if($pcate_id) {
            $where[] = ['pcate_id','=',$pcate_id];
        }
        try {
            $list = Db::table('mp_goods')->where($where)->field("id,name,origin_price,price,desc,pics")->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['cover'] = unserialize($v['pics'])[0];
            unset($v['pics']);
        }
        return ajax($list);
    }

    public function goodsDetail() {

    }

    public function cateList() {
        try {
            $map = [
                ['del','=',0],
                ['status','=',1]
            ];
            $list = Db::table('mp_goods_cate')->where($map)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $list = $this->recursion($list,0);
        return ajax($list);

    }










    private function recursion($array,$pid=0) {
        $to_array = [];
        foreach ($array as $v) {
            if($v['pid'] == $pid) {
                $v['child'] = $this->recursion($array,$v['id']);
                $to_array[] = $v;
            }
        }
        return $to_array;
    }

}