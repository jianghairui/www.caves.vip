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
            $where_attr = [
                ['goods_id','=',$id],
                ['del','=',0]
            ];
            $attr_list = Db::table('mp_goods_attr')->where($where_attr)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('attr_list',$attr_list);
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
            $val['use_attr'] = input('post.use_attr','');
            if($val['use_attr']) {
                $attr1 = input('post.attr1',[]);
                $attr2 = input('post.attr2',[]);
                $attr3 = input('post.attr3',[]);

                $val['attr'] = input('post.attr','');
                if(!$val['attr'] || empty($attr1)) {
                    return ajax('至少添加一个规格',-1);
                }
                if(count($attr1) !== count($attr2) || count($attr1) !== count($attr3)) {
                    return ajax('操作异常',-1);
                }
            }
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
            $imginfo = @getimagesize($image_array[0]);
            if($imginfo) {
                $val['width'] = $imginfo[0];
                $val['height'] = $imginfo[1];
            }else {
                $val['width'] = 1;
                $val['height'] = 1;
            }
            try {
                $new_id = Db::table('mp_goods')->insertGetId($val);
                if($val['use_attr']) {
                    $attr_insert = [];
                    foreach ($attr1 as $k=>$v) {
                        $data['goods_id'] = $new_id;
                        $data['value'] = $attr1[$k];
                        $data['price'] = $attr2[$k];
                        $data['stock'] = $attr3[$k];
                        $data['create_time'] = time();
                        $attr_insert[] = $data;
                    }
                    Db::table('mp_goods_attr')->insertAll($attr_insert);
                }
            }catch (\Exception $e) {
                foreach ($image_array as $v) {
                    @unlink($v);
                }
                return ajax($e->getMessage(),-1);
            }
            return ajax([],1);
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
            $val['use_attr'] = input('post.use_attr',0);
            if($val['use_attr']) {
                $attr0 = input('post.attr0',[]);
                $attr1 = input('post.attr1',[]);
                $attr2 = input('post.attr2',[]);
                $attr3 = input('post.attr3',[]);

                $val['attr'] = input('post.attr','');
                if(!$val['attr'] || empty($attr1)) {
                    return ajax('至少添加一个规格',-1);
                }
                if(count($attr1) !== count($attr2) || count($attr1) !== count($attr3) || count($attr1) !== count($attr0)) {
                    return ajax('操作异常',-1);
                }
            }
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
                $imginfo = @getimagesize($image_array[0]);
                if($imginfo) {
                    $val['width'] = $imginfo[0];
                    $val['height'] = $imginfo[1];
                }else {
                    $val['width'] = 1;
                    $val['height'] = 1;
                }
                Db::table('mp_goods')->where($map)->update($val);
                if($val['use_attr']) {
                    $attr_ids = Db::table('mp_goods_attr')->where('goods_id',$val['id'])->column('id');
                    $attr_insert = [];
                    foreach ($attr1 as $k=>$v) {
                        $data['goods_id'] = $val['id'];
                        $data['value'] = $attr1[$k];
                        $data['price'] = $attr2[$k];
                        $data['stock'] = $attr3[$k];
                        if($attr0[$k] == '') {
                            $data['create_time'] = time();
                            $attr_insert[] = $data;
                        }else {
                            Db::table('mp_goods_attr')->where('id','=',$attr0[$k])->update($data);
                        }

                    }
                    Db::table('mp_goods_attr')->insertAll($attr_insert);
                    $whereDelete = [];
                    foreach ($attr_ids as $v) {
                        if(!in_array($v,$attr0)) {
                            $whereDelete[] = $v;
                        }
                    }
                    if(!empty($whereDelete)) {
                        Db::table('mp_goods_attr')->where('id','in',$whereDelete)->update(['del'=>1]);
                    }
                }
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
        $param['search'] = input('param.search','');
        $param['status'] = input('param.status','');
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['refund_apply'] = input('param.refund_apply','');
        $page['query'] = http_build_query(input('param.'));
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = " 1";
        $order = " ORDER BY `id` DESC";
        $orderby = " ORDER BY `d`.`id` DESC";
        if($param['status'] !== '') {
            $where .= " AND status=" . $param['status'];
        }
        if($param['refund_apply']) {
            $where .= " AND refund_apply=" . $param['refund_apply'];
        }
        if($param['logmin']) {
            $where .= " AND create_time>=" . strtotime(date('Y-m-d 00:00:00',strtotime($param['logmin'])));
        }
        if($param['logmax']) {
            $where .= " AND create_time<=" . strtotime(date('Y-m-d 23:59:59',strtotime($param['logmax'])));
        }
        if($param['search']) {
            $where .= " AND (pay_order_sn LIKE '%".$param['search']."%' OR tel LIKE '%".$param['search']."%')";
        }
        try {
            $count = Db::query("SELECT count(id) AS total_count FROM mp_order o WHERE " . $where);
            $sql = "SELECT 
`o`.`id`,`o`.`pay_order_sn`,`o`.`trans_id`,`o`.`receiver`,`o`.`tel`,`o`.`address`,`o`.`pay_price`,`o`.`total_price`,`o`.`carriage`,`o`.`create_time`,`o`.`refund_apply`,`o`.`status`,`o`.`refund_apply`,`d`.`order_id`,`d`.`goods_id`,`d`.`num`,`d`.`unit_price`,`d`.`goods_name`,`d`.`attr`,`g`.`pics` 
FROM (SELECT * FROM mp_order WHERE " . $where . $order . " LIMIT ".($curr_page-1)*$perpage.",".$perpage.") `o` 
LEFT JOIN `mp_order_detail` `d` ON `o`.`id`=`d`.`order_id`
LEFT JOIN `mp_goods` `g` ON `d`.`goods_id`=`g`.`id`
" . $orderby;
            $list = Db::query($sql);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $count = $count[0]['total_count'];
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
                    $data['receiver'] = $li['receiver'];
                    $data['tel'] = $li['tel'];
                    $data['address'] = $li['address'];
                    $data['total_price'] = $li['total_price'];
                    $data['carriage'] = $li['carriage'];
                    $data['status'] = $li['status'];
                    $data['refund_apply'] = $li['refund_apply'];
                    $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
                    $data_child['goods_id'] = $li['goods_id'];
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
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $this->assign('param',$param);
        $this->assign('list',$newlist);
        $this->assign('page',$page);
        return $this->fetch();
    }
//订单发货
    public function orderSend() {
        $id = input('param.id');
        try {
            $where = [
                ['del','=',0]
            ];
            $list = Db::table('mp_tracking')->where($where)->select();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('id',$id);
        return $this->fetch();
    }
//确认发货
    public function deliver() {
        $val['tracking_name'] = input('post.tracking_name');
        $val['tracking_num'] = input('post.tracking_num');
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $where = [
                ['id','=',$val['id']],
                ['status','=',1]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax('订单不存在或状态已改变',-1);
            }
            $update_data = [
                'status' => 2,
                'send_time' => time(),
                'tracking_name' => $val['tracking_name'],
                'tracking_num' => $val['tracking_num']
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
//订单详情
    public function orderDetail() {
        die('还没写');
    }
//订单修改
    public function orderModPost() {

    }
//退款
    public function orderRefund() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $where = [
                ['id','=',$val['id']],
                ['status','in',[1,2,3]]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax('订单不存在或状态已改变',-1);
            }
            $pay_order_sn = $exist['pay_order_sn'];
//            $exist['pay_price'] = 0.01;
            $arr = [
                'appid' => $this->config['app_id'],
                'mch_id'=> $this->config['mch_id'],
                'nonce_str'=>$this->randomkeys(32),
                'sign_type'=>'MD5',
                'transaction_id'=> $exist['trans_id'],
                'out_trade_no'=> $pay_order_sn,
                'out_refund_no'=> 'r' . $pay_order_sn,
                'total_fee'=> floatval($exist['pay_price'])*100,
                'refund_fee'=> floatval($exist['pay_price'])*100,
                'refund_fee_type'=> 'CNY',
                'refund_desc'=> '订单退款',
                'notify_url'=> $_SERVER['REQUEST_SCHEME'] . '://'.$_SERVER['HTTP_HOST'].'/wxRefundNotify',
                'refund_account' => 'REFUND_SOURCE_UNSETTLED_FUNDS'
            ];

            $arr['sign'] = $this->getSign($arr);
            $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
            $res = $this->curl_post_datas($url,$this->array2xml($arr),true);
            if($res && $res['return_code'] == 'SUCCESS') {
                if($res['result_code'] == 'SUCCESS') {
                    $update_data = [
                        'refund_apply' => 2,
                        'refund_time' => time()
                    ];
                    Db::table('mp_order')->where($where)->update($update_data);
                    return ajax();
                }else {
                    return ajax($res['err_code_des'],-1);
                }
            }else {
                return ajax('退款通知失败',-1);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
    }
//删除订单
    public function orderDel() {

    }

    public function modAdress() {
        $val['address'] = input('post.address');
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax('订单不存在或状态已改变',-1);
            }
            $update_data = [
                'address' => $val['address']
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    public function modPrice() {
        $val['pay_price'] = input('post.pay_price');
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax('订单不存在或状态已改变',-1);
            }
            $update_data = [
                'pay_price' => $val['pay_price']
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

}