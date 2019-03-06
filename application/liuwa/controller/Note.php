<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/1/9
 * Time: 11:09
 */
namespace  app\liuwa\controller;
use think\Db;
use think\facade\Request;
class Note extends Common {

    public function noteList() {
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [];
        if($param['search']) {
            $where[] = ['title','like',"%{$param['search']}%"];
        }
        $count = Db::table('mp_note')->where($where)->count();

        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('mp_note')->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function noteAdd() {
        return $this->fetch();
    }

    public function noteAddPost() {
        if(Request::isAjax()) {
            $val['goods_name'] = input('post.goods_name');
            $val['price'] = input('post.price');
            $val['neednum'] = input('post.neednum');
            $val['status'] = input('post.status');
            $val['create_time'] = time();
            $this->checkPost($val);

            if(isset($_FILES['file1'])) {
                $info = $this->upload('file1');
                if($info['error'] === 0) {
                    $val['pic'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }

            try {
                $res = Db::table('free_goods')->insert($val);
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }
            if($res) {
                return ajax([],1);
            }else {
                if(isset($_FILES['file1'])) {
                    @unlink('.'.$val['pic']);
                }
                return ajax('添加失败',-1);
            }
        }
    }

    public function noteDetail() {
        $id = input('param.id');
        $info = Db::table('free_goods')->where('id','=',$id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function noteModPost() {
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





}