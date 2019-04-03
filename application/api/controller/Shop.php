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
    //商城主页顶部分类
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
    //商品列表
    public function goodsList() {
        $pcate_id = input('post.pcate_id',0);
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $where = [
            ['status','=',1],
            ['del','=',0]
        ];
        if($pcate_id) {
            $where[] = ['pcate_id','=',$pcate_id];
        }
        try {
            $list = Db::table('mp_goods')->where($where)->field("id,name,origin_price,price,desc,pics")->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['cover'] = unserialize($v['pics'])[0];
            unset($v['pics']);
        }
        return ajax($list);
    }
    //商品详情
    public function goodsDetail() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $info = Db::table('mp_goods')
                ->where($where)
                ->field("id,name,detail,origin_price,price,vip_price,pics,carriage,stock,sales,desc,use_attr,attr,hot")
                ->find();
            if(!$info) {
                return ajax($val['id'],-4);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $info['pics'] = unserialize($info['pics']);
        return ajax($info);
    }
    //获取分类
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
    //购买下单
    public function purchase() {
        $val['goods_id'] = input('post.goods_id');
        $val['num'] = input('post.num');
        $val['receiver'] = input('post.receiver');
        $val['tel'] = input('post.tel');
        $val['address'] = input('post.address');
        $this->checkPost($val);
        $val['attr'] = input('post.attr','');
        $val['uid'] = $this->myinfo['uid'];
        if(!is_numeric($val['num'])) {
            return ajax($val['num'],-4);
        }
        try {
            $goods_exist = Db::table('mp_goods')->where('id', $val['goods_id'])->find();
            if (!$goods_exist) {
                return ajax('invalid goods_id', -4);
            }
            if($val['num'] > $goods_exist['stock']) {
                return ajax('库存不足',39);
            }
            $val['price'] = $goods_exist['price'];
            $val['goods_name'] = $goods_exist['name'];
            $val['carriage'] = $goods_exist['carriage'];
            $val['real_price'] = $goods_exist['price'] * $val['num'] + $goods_exist['carriage'];
            $val['create_time'] = time();
            $val['order_sn'] = gen_unique_number('');
            $val['pay_order_sn'] = create_unique_number('');
            Db::startTrans();
            Db::table('mp_order')->insert($val);
            Db::table('mp_goods')->where('id', $val['goods_id'])->setDec('stock',$val['num']);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(), -1);
        }
        return ajax($val['pay_order_sn']);
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