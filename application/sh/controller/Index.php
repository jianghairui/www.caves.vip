<?php
namespace app\sh\controller;
use my\Auth;
use think\Db;
use think\Exception;
use EasyWeChat\Factory;

class Index extends Common
{
    //首页
    public function index() {
        $auth = new Auth();
        $authlist = $auth->getAuthList(session('admin_id'));
        $this->assign('authlist',$authlist);
        return $this->fetch();
    }
    //分类列表
    public function articleList()
    {
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [];
        if($param['search']) {
            $where[] = ['title|desc','like',"%{$param['search']}%"];
        }
        $count = Db::table('mp_article')->where($where)->count();

        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('mp_article')->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    //添加分类页面
    public function articleAdd() {
        return $this->fetch();
    }
    //添加分类提交
    public function articleAddPost() {
        $val['cate_name'] = input('post.cate_name');
        $val['pid'] = input('post.pid');

        $this->checkPost($val);
        if($val['pid'] != 0) {
            $where[] = ['pid','=',0];
            $where[] = ['id','=',$val['pid']];
            $exist = Db::table('mp_cate')->where($where)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
        }
        $map[] = ['cate_name','=',$val['cate_name']];
        $map[] = ['pid','=',$val['pid']];
        if($this->checkExist('mp_cate',$map)) {
            return ajax('分类已存在',-1);
        }

        foreach ($_FILES as $k=>$v) {
            if($v['name'] == '') {
                unset($_FILES[$k]);
            }
        }
        if(!empty($_FILES)) {
            $info = $this->upload(array_keys($_FILES)[0]);
            if($info['error'] === 0) {
                $val['cover'] = $info['data'];
            }else {
                return ajax($info['msg'],-1);
            }
        }
        $res = Db::table('mp_cate')->insert($val);
        if($res) {
            return ajax([]);
        }else {
            if(isset($val['cover'])) {
                @unlink($val['cover']);
            }
            return ajax('添加失败',-1);
        }
    }
    //修改分类页面
    public function articleMod() {
        $cate_id = input('param.cate_id') ? input('param.cate_id') : 0;
        $exist = Db::table('mp_cate')->where('id',$cate_id)->find();
        if(!$exist) {
            $this->error('非法操作');
        }
        $this->assign('cate',$exist);
        return $this->fetch();
    }
    //修改分类提交
    public function articleModPost() {
        $val['cate_name'] = input('post.cate_name');
        $val['id'] = input('post.cate_id');
        $this->checkPost($val);

        $exist = Db::table('mp_cate')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        if($this->checkExist('mp_cate',[
            ['cate_name','=',$val['cate_name']],
            ['id','<>',$val['id']]
        ])) {
            return ajax('分类已存在',-1);
        }
        foreach ($_FILES as $k=>$v) {
            if($v['name'] == '') {
                unset($_FILES[$k]);
            }
        }

        if(!empty($_FILES)) {
            $info = $this->upload(array_keys($_FILES)[0]);
            if($info['error'] === 0) {
                $val['cover'] = $info['data'];
            }else {
                return ajax($info['msg'],-1);
            }
        }

        $res = Db::table('mp_cate')->update($val);
        if($res !== false) {
            if(!empty($_FILES)) {
                @unlink($exist['cover']);
            }
            return ajax([]);
        }else {
            if(!empty($_FILES)) {
                @unlink($val['cover']);
            }
            return ajax('修改失败',-1);
        }
    }
    //删除分类
    public function articleDel() {
        $val['id'] = input('post.cate_id');
        $this->checkPost($val);
        $exist = Db::table('mp_cate')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        $model = model('Cate');
        if($exist['pid'] == 0) {
            $child_ids = Db::table('mp_cate')->where('pid','eq',$val['id'])->column('id');
            try {
                $model::destroy($child_ids);
            }catch (Exception $e) {
                return ajax($e->getMessage(),-1);
            }
        }
        try {
            $model::destroy($val['id']);
        }catch (Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        return ajax([],1);
    }
    //停用分类
    public function articleHide() {
        $val['id'] = input('post.cate_id');
        $this->checkPost($val);
        $exist = Db::table('mp_cate')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_cate')->where('id',$val['id'])->update(['status'=>0]);
        if($res !== false) {
            if($exist['pid'] == 0) {
                Db::table('mp_cate')->where('pid',$val['id'])->update(['status'=>0]);
            }
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }
    //启用分类
    public function articleShow() {
        $val['id'] = input('post.cate_id');
        $this->checkPost($val);
        $exist = Db::table('mp_cate')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_cate')->where('id',$val['id'])->update(['status'=>1]);
        if($res !== false) {
            if($exist['pid'] == 0) {
                Db::table('mp_cate')->where('pid',$val['id'])->update(['status'=>1]);
            }
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }


}
