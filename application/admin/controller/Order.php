<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/1/9
 * Time: 11:09
 */
namespace  app\admin\controller;
use think\Db;
class Order extends Common {

    public function orderList() {
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');
        $param['status'] = input('param.status','');
        $page['query'] = http_build_query(input('param.'));
        $where = [];

        if(!is_null($param['status']) && $param['status'] !== '') {
            $where[] = ['o.status','=',$param['status']];
        }

        if($param['logmin']) {
            $where[] = ['o.created_at','>=',date('Y-m-d 00:00:00',strtotime($param['logmin']))];
        }

        if($param['logmax']) {
            $where[] = ['o.created_at','<=',date('Y-m-d 23:59:59',strtotime($param['logmax']))];
        }

        if($param['search']) {
            $where[] = ['g.goods_name|o.pay_order_sn|o.order_sn','like',"%{$param['search']}%"];
        }

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $count = Db::table('orders')->alias('o')
            ->join("goods g","o.goods_id=g.id","left")
            ->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('orders')->alias("o")
                ->join("goods g","o.goods_id=g.id","left")
                ->where($where)
                ->field("o.*,g.goods_name")
                ->order(['created_at'=>'DESC'])
                ->limit(($curr_page - 1)*$perpage,$perpage)
                ->select();
        }catch (\Exception $e) {
            exit('SQL错误: ' . $e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('status',$param['status']);
        return $this->fetch();
    }

    public function orderDetail() {
        $order_id = input('param.id');
        $where = [
            ['o.id','=',$order_id]
        ];
        try {
            $info = Db::table('orders')->alias("o")
                ->join("goods g","o.goods_id=g.id","left")
                ->where($where)
                ->field("o.*,g.goods_name,g.image_url")
                ->find();
        }catch (\Exception $e) {
            exit('SQL错误: ' . $e->getMessage());
        }

        $this->assign('info',$info);
        return $this->fetch();
    }



}