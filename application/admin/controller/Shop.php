<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/19
 * Time: 13:45
 */
namespace app\admin\controller;
use think\Db;
use think\facade\Request;

class Shop extends Common {
//商品列表
    public function goodsList() {
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [];
        if($param['search']) {
            $where[] = ['name','like',"%{$param['search']}%"];
        }
        $count = Db::table('mp_goods')->where($where)->count();

        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('mp_goods')
                ->where($where)
                ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
//添加商品
    public function goodsAdd() {
        try {
            $where = [
                ['pid','=',0],
                ['del','=',0],
                ['status','=',1]
            ];
            $list = Db::table('mp_goods_cate')->where($where)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
//添加修改商品时获取分类列表
    public function getCateList() {
        $pid = input('post.pid');
        $where = [
            ['pid','=',$pid],
            ['del','=',0],
            ['status','=',1]
        ];
        try {
            $list = Db::table('mp_goods_cate')->where($where)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }
//商品详情
    public function goodsDetail() {
        $id = input('param.id');
        try {
            $where = [
                ['pid','=',0],
                ['del','=',0],
                ['status','=',1]
            ];
            $info = Db::table('mp_goods')->where('id','=',$id)->find();
            $list = Db::table('mp_goods_cate')->where($where)->select();

            $where = [
                ['pid','=',$info['pcate_id']],
                ['del','=',0],
                ['status','=',1]
            ];
            $child = Db::table('mp_goods_cate')->where($where)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('child',$child);
        $this->assign('info',$info);
        return $this->fetch();
    }
//添加商品POST
    public function goodsAddPost() {
        if(Request::isAjax()) {
            $val['pcate_id'] = input('post.pcate_id');
            $val['cate_id'] = input('post.cate_id');
            $val['name'] = input('post.name');
            $val['origin_price'] = input('post.origin_price');
            $val['price'] = input('post.price');
            $val['stock'] = input('post.stock');
            $val['sort'] = input('post.sort');
            $val['hot'] = input('post.hot');
            $val['sales'] = input('post.sales');
            $val['status'] = input('post.status');
            $val['unit'] = input('post.unit');
            $val['carriage'] = input('post.carriage');
            $val['reduction'] = input('post.reduction');
            $val['service'] = input('post.service');
            $val['desc'] = input('post.desc');
            $val['status'] = input('post.status');
            $val['create_time'] = time();
            $this->checkPost($val);
            $val['detail'] = input('post.detail');
            $image = input('post.pic_url',[]);
            $image_array = [];
            if(is_array($image) && !empty($image)) {
                if(count($image) > 5) {
                    return ajax('最多上传5张图片',-1);
                }
                foreach ($image as $v) {
                    if(!file_exists($v)) {
                        return ajax('无效的图片路径',-1);
                    }
                }
                foreach ($image as $v) {
                    $image_array[] = $this->rename_file($v);
                }
            }else {
                return ajax('请上传图片',-1);
            }
            $val['pics'] = serialize($image_array);
            try {
                $res = Db::table('mp_goods')->insert($val);
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }
            if($res) {
                return ajax([],1);
            }else {
                foreach ($image_array as $v) {
                    @unlink($v);
                }
                return ajax('添加失败',-1);
            }
        }
    }
//修改商品POST
    public function goodsModPost() {
        if(Request::isAjax()) {
            $val['pcate_id'] = input('post.pcate_id');
            $val['cate_id'] = input('post.cate_id');
            $val['name'] = input('post.name');
            $val['origin_price'] = input('post.origin_price');
            $val['price'] = input('post.price');
            $val['stock'] = input('post.stock');
            $val['sort'] = input('post.sort');
            $val['hot'] = input('post.hot');
            $val['sales'] = input('post.sales');
            $val['status'] = input('post.status');
            $val['unit'] = input('post.unit');
            $val['carriage'] = input('post.carriage');
            $val['reduction'] = input('post.reduction');
            $val['service'] = input('post.service');
            $val['desc'] = input('post.desc');
            $val['status'] = input('post.status');
            $val['id'] = input('post.id');
            $val['create_time'] = time();
            $this->checkPost($val);
            $val['detail'] = input('post.detail');

            $image = input('post.pic_url',[]);
            try {
                $map = [
                    ['id','=',$val['id']],
                    ['del','=',0]
                ];
                $exist = Db::table('mp_goods')->where($map)->find();
                if(!$exist) {
                    return ajax('非法参数',-1);
                }
                $old_pics = unserialize($exist['pics']);
                $image_array = [];
                if(is_array($image) && !empty($image)) {
                    if(count($image) > 5) {
                        return ajax('最多上传5张图片',-1);
                    }
                    foreach ($image as $v) {
                        if(!file_exists($v)) {
                            return ajax('无效的图片路径',-1);
                        }
                    }
                    foreach ($image as $v) {
                        $image_array[] = $this->rename_file($v);
                    }
                }else {
                    return ajax('请上传图片',-1);
                }
                $val['pics'] = serialize($image_array);
                Db::table('mp_goods')->where($map)->update($val);
            }catch (\Exception $e) {
                foreach ($image_array as $v) {
                    if(!in_array($v,$old_pics)) {
                        @unlink($v);
                    }
                }
                return ajax($e->getMessage(),-1);
            }
            foreach ($old_pics as $v) {
                if(!in_array($v,$image_array)) {
                    @unlink($v);
                }
            }
            return ajax([],1);
        }
    }
//下架
    public function goodsHide() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id],
            ['status','=',1]
        ];
        try {
            $res = Db::table('mp_goods')->where($map)->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax();
        }else {
            return ajax('共修改0条记录',-1);
        }
    }
//上架
    public function goodsShow() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id],
            ['status','=',0]
        ];
        try {
            $res = Db::table('mp_goods')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax();
        }else {
            return ajax('共修改0条记录',-1);
        }
    }
//删除商品
    public function goodsDel() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id]
        ];
        try {
            $res = Db::table('mp_goods')->where($map)->update(['del'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax();
        }else {
            return ajax('共修改0条记录',-1);
        }
    }
//分类列表
    public function cateList() {
        $pid = input('param.pid',0);
        $where = [
            ['pid','=',$pid],
            ['del','=',0]
        ];
        try {
            $list = Db::table('mp_goods_cate')->where($where)->select();
            $pcate_name = Db::table('mp_goods_cate')->where('id',$pid)->value('cate_name');
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('pcate_name',$pcate_name);
        $this->assign('pid',$pid);
        $this->assign('list',$list);
        return $this->fetch();
    }
//添加分类
    public function cateAdd() {
        $pid = input('param.pid',0);
        try {
            $list = Db::table('mp_goods_cate')->where('pid',0)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('pid',$pid);
        return $this->fetch();
    }
//添加分类POST
    public function cateAddPost() {
        $val['cate_name'] = input('post.cate_name');
        $val['pid'] = input('post.pid',0);
        $this->checkPost($val);

        if(isset($_FILES['file'])) {
            $info = $this->upload('file');
            if($info['error'] === 0) {
                $val['icon'] = $info['data'];
            }else {
                return ajax($info['msg'],-1);
            }
        }
        try {
            Db::table('mp_goods_cate')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['icon'])) {
                @unlink($val['icon']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([]);
    }
//分类详情
    public function cateDetail() {
        $id = input('param.id');
        try {
            $info = Db::table('mp_goods_cate')->where('id',$id)->find();
            $list = Db::table('mp_goods_cate')->where('pid',0)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$info);
        $this->assign('list',$list);
        return $this->fetch();
    }
//修改分类POST
    public function cateModPost() {
        $val['cate_name'] = input('post.cate_name');
        $val['pid'] = input('post.pid',0);
        $val['id'] = input('post.id',0);
        $this->checkPost($val);
        try {
            $exist = Db::table('mp_goods_cate')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            if(isset($_FILES['file'])) {
                $info = $this->upload('file');
                if($info['error'] === 0) {
                    $val['icon'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            Db::table('mp_goods_cate')->where('id',$val['id'])->update($val);
        }catch (\Exception $e) {
            if(isset($val['icon'])) {
                @unlink($val['icon']);
            }
            return ajax($e->getMessage(),-1);
        }
        if(isset($val['icon'])) {
            @unlink($exist['icon']);
        }
        return ajax([]);
    }
//隐藏分类
    public function cateHide() {
        $id = input('post.id');
        try {
            $exist = Db::table('mp_goods_cate')->where('id',$id)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_goods_cate')->where('id',$id)->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//显示分类
    public function cateShow() {
        $id = input('post.id');
        try {
            $exist = Db::table('mp_goods_cate')->where('id',$id)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_goods_cate')->where('id',$id)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//删除分类
    public function cateDel() {
        $id = input('post.id');
        try {
            $exist = Db::table('mp_goods_cate')->where('id',$id)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_goods_cate')->where('id',$id)->update(['del'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//订单列表
    public function orderList() {
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [];
        if($param['search']) {
            $where[] = ['o.pay_order_sn|o.tel','like',"%{$param['search']}%"];
        }
        $count = Db::table('mp_order')->alias('o')->where($where)->count();
        try {
            $list = Db::table('mp_order')->alias('o')
                ->join("mp_order_detail d","o.id=d.order_id","left")
                ->join("mp_goods g","d.goods_id=g.id","left")
                ->order(['id'=>'DESC'])
                ->where($where)
                ->field("o.id,o.trans_id,o.pay_order_sn,o.pay_price,o.total_price,o.carriage,o.create_time,o.receiver,o.tel,o.address,o.refund_apply,o.status,d.order_id,d.num,d.unit_price,d.goods_name,d.attr,g.pics")
                ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        $order_id = [];
        $newlist = [];
        foreach ($list as $v) {
            $order_id[] = $v['id'];
        }
        $uniq_order_id = array_unique($order_id);
        foreach ($uniq_order_id as $v) {
            $child = [];
            foreach ($list as $li) {
                if($li['order_id'] == $v) {
                    $data['id'] = $li['id'];
                    $data['pay_order_sn'] = $li['pay_order_sn'];
                    $data['pay_price'] = $li['pay_price'];
                    $data['trans_id'] = $li['trans_id'];
                    $data['status'] = $li['status'];
                    $data['refund_apply'] = $li['refund_apply'];
                    $data['receiver'] = $li['receiver'];
                    $data['tel'] = $li['tel'];
                    $data['address'] = $li['address'];
                    $data['total_price'] = $li['total_price'];
                    $data['carriage'] = $li['carriage'];
                    $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
                    $data_child['cover'] = unserialize($li['pics'])[0];
                    $data_child['goods_name'] = $li['goods_name'];
                    $data_child['num'] = $li['num'];
                    $data_child['unit_price'] = $li['unit_price'];
                    $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                    $data_child['attr'] = $li['attr'];
                    $child[] = $data_child;
                }
            }
            $data['child'] = $child;
            $newlist[] = $data;
        }
//        halt($newlist);
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $this->assign('list',$newlist);
        $this->assign('page',$page);
        return $this->fetch();
    }
//确认发货
    public function deliver() {

    }
//订单详情
    public function orderDetail() {

    }
//订单修改
    public function orderModPost() {

    }
//退款
    public function orderRefund() {

    }
//删除订单
    public function orderDel() {

    }

}