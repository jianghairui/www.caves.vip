<?php
namespace app\admin\controller;
use think\Db;
use think\Exception;
use think\facade\Request;

class Index extends Common
{
    //首页
    public function index() {
        return $this->fetch();
    }

    public function slideList() {
        try {
            $list = Db::table('slideshow')->order(['sort'=>'ASC'])->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    //添加轮播图POST
    public function slideadd() {
        if(Request::isAjax()) {
            $val['title'] = input('post.title');
            $this->checkPost($val);
            $val['url'] = input('post.url');
            $val['type'] = input('post.type');

            if(isset($_FILES['file-2'])) {
                $info = $this->upload('file-2');
                if($info['error'] === 0) {
                    $val['pic'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }else {
                return ajax('请上传图片',-1);
            }

            try {
                $res = Db::table('slideshow')->insert($val);
            }catch (\Exception $e) {
                if(isset($val['pic'])) {
                    @unlink('.'.$val['pic']);
                }
                return ajax($e->getMessage(),-1);
            }
            if($res) {
                return ajax([],1);
            }else {
                if(isset($val['pic'])) {
                    @unlink('.'.$val['pic']);
                }
                return ajax('添加失败',-1);
            }
        }
    }
    //修改轮播图
    public function slidemod() {
        $val['id'] = input('param.id');
        $exist = Db::table('slideshow')->where('id','=',$val['id'])->find();
        if(!$exist) {
            $this->error('非法操作',url('Banner/slideshow'));
        }
        $this->assign('info',$exist);
        return $this->fetch();
    }
    //修改轮播图POST
    public function slidemod_post() {
        if(Request::isAjax()) {
            $val['title'] = input('post.title');
            $val['id'] = input('post.slideid');
            $val['type'] = input('post.type');
            $this->checkPost($val);
            $val['url'] = input('post.url');

            $exist = Db::table('slideshow')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }

            if(isset($_FILES['file-2'])) {
                $info = $this->upload('file-2');
                if($info['error'] === 0) {
                    $val['pic'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            try {
                Db::table('slideshow')->update($val);
            }catch (\Exception $e) {
                if(isset($_FILES['file-2'])) {
                    @unlink('.'.$val['pic']);
                }
                return ajax($e->getMessage(),-1);
            }
            if(isset($_FILES['file-2'])) {
                @unlink('.'.$exist['pic']);
            }
            return ajax([],1);
        }
    }
    //删除轮播图
    public function slide_del() {
        $val['id'] = input('post.slideid');
        $this->checkPost($val);
        $exist = Db::table('slideshow')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        $model = model('Slideshow');
        try {
            $model::destroy($val['id']);
        }catch (Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        return ajax([],1);
    }
    //轮播图排序
    public function sortSlide() {
        $val['id'] = input('post.id');
        $val['sort'] = input('post.sort');
        $this->checkPost($val);
        try {
            Db::table('slideshow')->update($val);
        }catch (Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val);
    }
    //禁用轮播图
    public function slide_stop() {
        $val['id'] = input('post.slideid');
        $this->checkPost($val);
        $exist = Db::table('slideshow')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        try {
            Db::table('slideshow')->where('id',$val['id'])->update(['status'=>0]);
        }catch (Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //启用轮播图
    public function slide_start() {
        $val['id'] = input('post.slideid');
        $this->checkPost($val);
        $exist = Db::table('slideshow')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        try {
            Db::table('slideshow')->where('id',$val['id'])->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }


    public function test() {
        echo 'HELLO';
    }


}
