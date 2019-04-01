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
            $list = Db::table('mp_goods')->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }

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

    public function goodsDetail() {
        $id = input('param.id');
        try {
            $info = Db::table('free_goods')->where('id','=',$id)->find();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$info);
        return $this->fetch();
    }

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

            $val['detail'] = input('post.detail');
            $val['status'] = input('post.status');
            $val['create_time'] = time();
            $this->checkPost($val);
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

    public function goodsModPost() {
        if(Request::isAjax()) {
            $val['goods_name'] = input('post.goods_name');
            $val['price'] = input('post.price');
            $val['neednum'] = input('post.neednum');
            $val['status'] = input('post.status');
            $val['id'] = input('post.id');
            $this->checkPost($val);

            try {
                $exist = Db::table('free_goods')->where('id','=',$val['id'])->find();
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }

            if(!$exist) {
                return ajax('非法参数',-1);
            }

            if(isset($_FILES['file1'])) {
                $info = $this->upload('file1');
                if($info['error'] === 0) {
                    $val['pic'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }

            try {
                $res = Db::table('free_goods')->where('id','=',$val['id'])->update($val);
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }
            if($res !== false) {
                if(isset($_FILES['file1'])) {
                    @unlink('.'.$exist['pic']);
                }
                return ajax([],1);
            }else {
                if(isset($_FILES['file1'])) {
                    @unlink('.'.$val['pic']);
                }
                return ajax('保存失败',-1);
            }
        }
    }

    public function goodsHide() {
        $id = input('post.id','0');
        $map = [
            ['id','=',$id],
            ['status','=',1]
        ];
        try {
            $res = Db::table('free_goods')->where($map)->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax();
        }else {
            return ajax('共修改0条记录',-1);
        }
    }

    public function goodsShow() {

    }

    public function goodsDel() {

    }

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





}