<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/1/9
 * Time: 11:09
 */
namespace  app\admin\controller;
use think\Db;
use think\facade\Request;
class Store extends Common {

    public function storeList() {
        $param['status'] = input('param.status','');
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',5);

        $where = [];

        if(!is_null($param['status']) && $param['status'] !== '') {
            $where[] = ['s.status','=',$param['status']];
        }

        if($param['logmin']) {
            $where[] = ['s.created_at','>=',date('Y-m-d 00:00:00',strtotime($param['logmin']))];
        }

        if($param['logmax']) {
            $where[] = ['s.created_at','<=',date('Y-m-d 23:59:59',strtotime($param['logmax']))];
        }

        if($param['search']) {
            $where[] = ['s.name','like',"%{$param['search']}%"];
        }

        $count = Db::table('store')->alias('s')->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('store')->alias('s')
                ->join("user u","s.uid=u.id","left")
                ->where($where)
                ->field("s.*,u.nickname,u.mobile")
                ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('status',$param['status']);
        return $this->fetch();

    }

    public function storeModPost() {
        if(Request::isAjax()) {
            $val['name'] = input('post.name');
            $val['introduction'] = input('post.introduction');
            $val['status'] = input('post.status');
            $val['id'] = input('post.id');
            $val['updated_at'] = date("Y-m-d H:i:s");

            $this->checkPost($val);

            if(isset($_FILES['file1'])) {
                $info = $this->upload('file1');
                if($info['error'] === 0) {
                    $val['logo_pic_url'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }

            if(isset($_FILES['file2'])) {
                $info = $this->upload('file2');
                if($info['error'] === 0) {
                    $val['prove_url'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            try {
                $res = Db::table('store')->where('id','=',$val['id'])->update($val);
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }
            if($res) {
                return ajax([],1);
            }else {
                return ajax('保存失败',-1);
            }
        }
    }

    public function storeDetail() {
        $id = input('param.id');
        $info = Db::table('store')->where('id','=',$id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function storePass() {
        $map[] = ['status','=',0];
        $map[] = ['id','=',input('post.id',0)];

        $exist = Db::table('store')->where($map)->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        try {
            Db::table('store')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    public function goodsList() {
        $id = input('param.id');
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $store_name = Db::table('store')->where('id','=',$id)->value('name');
        $where = [];
        if($id) {
            $where = [
                ['store_id','=',$id]
            ];
        }

        if($param['search']) {
            $where[] = ['goods_name','like',"%{$param['search']}%"];
        }
        $count = Db::table('goods')->where($where)->count();

        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('goods')->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();

        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('id',$id);
        $this->assign('store_name',$store_name);

        return $this->fetch();
    }

    public function goodsHide() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id],
            ['status','=',1]
        ];
        try {
            Db::table('goods')->where($map)->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function goodsShow() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id],
            ['status','=',0]
        ];
        try {
            Db::table('goods')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function goodsAdd() {
        $id = input('param.store_id');
        $store_name = Db::table('store')->where('id','=',$id)->value('name');
        if(!$store_name) {
            die('无效的参数');
        }
        $this->assign('store_name',$store_name);
        $this->assign('store_id',$id);
        return $this->fetch();
    }

    public function goodsAddPost() {
        if(Request::isAjax()) {

            $val['goods_name'] = input('post.goods_name');
            $val['goods_info'] = input('post.goods_info');
            $val['store_id'] = input('post.store_id');
            $val['price'] = input('post.price');
            $val['stock'] = input('post.stock');
            $val['is_shipping'] = input('post.is_shipping');
            $val['status'] = input('post.status');
            $val['postage'] = input('post.postage');
            $val['created_at'] = date('Y-m-d H:i:s');

            $this->checkPost($val);

            if(isset($_FILES['file1'])) {
                $info = $this->upload('file1');
                if($info['error'] === 0) {
                    $val['image_one'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }

            if(isset($_FILES['file2'])) {
                $info = $this->upload('file2');
                if($info['error'] === 0) {
                    $val['image_two'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            if(isset($_FILES['file3'])) {
                $info = $this->upload('file3');
                if($info['error'] === 0) {
                    $val['image_three'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            if(isset($_FILES['file4'])) {
                $info = $this->upload('file4');
                if($info['error'] === 0) {
                    $val['image_four'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            try {
                $res = Db::table('goods')->insert($val);
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }
            if($res) {
                return ajax([],1);
            }else {
                return ajax('添加失败',-1);
            }
        }
    }

    public function goodsDetail() {
        $id = input('param.id');
        $info = Db::table('goods')->where('id','=',$id)->find();
        $store_name = Db::table('store')->where('id','=',$info['store_id'])->value('name');
        $this->assign('info',$info);
        $this->assign('store_name',$store_name);
        return $this->fetch();
    }

    public function goodsModPost() {
        if(Request::isAjax()) {

            $val['goods_name'] = input('post.goods_name');
            $val['goods_info'] = input('post.goods_info');
            $val['store_id'] = input('post.store_id');
            $val['price'] = input('post.price');
            $val['stock'] = input('post.stock');
            $val['is_shipping'] = input('post.is_shipping');
            $val['status'] = input('post.status');
            $val['postage'] = input('post.postage');
            $val['created_at'] = date('Y-m-d H:i:s');
            $val['id'] = input('post.id');

            $this->checkPost($val);

            if(isset($_FILES['file1'])) {
                $info = $this->upload('file1');
                if($info['error'] === 0) {
                    $val['image_one'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }

            if(isset($_FILES['file2'])) {
                $info = $this->upload('file2');
                if($info['error'] === 0) {
                    $val['image_two'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            if(isset($_FILES['file3'])) {
                $info = $this->upload('file3');
                if($info['error'] === 0) {
                    $val['image_three'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            if(isset($_FILES['file4'])) {
                $info = $this->upload('file4');
                if($info['error'] === 0) {
                    $val['image_four'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            try {
                $res = Db::table('goods')->where('id','=',$val['id'])->update($val);
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }
            if($res) {
                return ajax([],1);
            }else {
                return ajax('保存失败',-1);
            }
        }
    }





}