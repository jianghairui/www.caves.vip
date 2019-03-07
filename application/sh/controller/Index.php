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
            $where[] = ['a.title|a.desc','like',"%{$param['search']}%"];
        }
        $count = Db::table('mp_article')->alias('a')->where($where)->count();

        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('mp_article')->alias('a')
                ->join('mp_admin ad','a.admin_id=ad.id','left')
                ->field('a.*,ad.realname')
                ->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    //添加分类页面
    public function articleAdd() {
        $tag = Db::table('mp_tag')->select();
        $this->assign('tag',$tag);
        return $this->fetch();
    }
    //添加分类提交
    public function articleAddPost() {
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        $val['status'] = input('post.status');
        $this->checkPost($val);
        $val['content'] = input('post.content');
        $val['admin_id'] = session('admin_id');
        $val['tags'] = input('post.tag',[]);
        $val['tags'] = implode(',',$val['tags']);

        foreach ($_FILES as $k=>$v) {
            if($v['name'] == '') {
                unset($_FILES[$k]);
            }
        }
        if(!empty($_FILES)) {
            $info = $this->upload(array_keys($_FILES)[0]);
            if($info['error'] === 0) {
                $val['pic'] = $info['data'];
            }else {
                return ajax($info['msg'],-1);
            }
        }
        try {
            Db::table('mp_article')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([]);

    }
    //修改分类页面
    public function articleDetail() {
        $article_id = input('param.id');
        $exist = Db::table('mp_article')->where('id',$article_id)->find();
        if(!$exist) {
            $this->error('非法操作');
        }
        $tag = Db::table('mp_tag')->select();
        $this->assign('tag',$tag);
        $this->assign('my_tag',explode(',',$exist['tags']));
        $this->assign('info',$exist);
        return $this->fetch();
    }
    //修改分类提交
    public function articleModPost() {
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        $val['status'] = input('post.status');
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $val['content'] = input('post.content');
        $val['admin_id'] = session('admin_id');
        $val['tags'] = input('post.tag',[]);
        $val['tags'] = implode(',',$val['tags']);

        foreach ($_FILES as $k=>$v) {
            if($v['name'] == '') {
                unset($_FILES[$k]);
            }
        }
        if(!empty($_FILES)) {
            $info = $this->upload(array_keys($_FILES)[0]);
            if($info['error'] === 0) {
                $val['pic'] = $info['data'];
            }else {
                return ajax($info['msg'],-1);
            }
        }
        try {
            $exist = Db::table('mp_article')->where('id',$val['id'])->find();
            $res = Db::table('mp_article')->update($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        if($res !== false) {
            if(!empty($_FILES)) {
                @unlink($exist['pic']);
            }
            return ajax();
        }else {
            if(!empty($_FILES)) {
                @unlink($val['pic']);
            }
            return ajax('修改失败',-1);
        }

    }
    //删除分类
    public function articleDel() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_article')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        $model = model('Article');
        try {
            $model::destroy($val['id']);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        return ajax([],1);
    }
    //停用分类
    public function articleHide() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_article')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_article')->where('id',$val['id'])->update(['status'=>0]);
        if($res !== false) {
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }
    //启用分类
    public function articleShow() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_article')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_article')->where('id',$val['id'])->update(['status'=>1]);
        if($res !== false) {
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }

    public function tagList() {
        $tag = Db::table('mp_tag')->select();
        $this->assign('list',$tag);
        return $this->fetch();
    }

    public function tagAdd() {
        return $this->fetch();
    }

    public function tagAddPost() {
        $val['tag_name'] = input('post.tag_name');
        try {
            Db::table('mp_tag')->insert($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val,1);
    }

    public function tagMod() {
        $id = input('param.id');
        $exist = Db::table('mp_tag')->where('id',$id)->find();
        if(!$exist) {
            $this->error('非法参数');
        }
        $this->assign('info',$exist);
        return $this->fetch();
    }

    public function tagModPost() {
        $val['tag_name'] = input('post.tag_name');
        $val['id'] = input('post.id');
        try {
            $exist = Db::table('mp_tag')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_tag')->where('id',$val['id'])->update($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val,1);
    }

    public function tagDel() {
        $val['id'] = input('post.id');
        try {
            $exist = Db::table('mp_tag')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            Db::table('mp_tag')->where('id',$val['id'])->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val,1);
    }


    private function get_tag_arr($arr) {
        $array = [];
        foreach ($arr as $v) {
            $array[$v['id']] = $v['tag_name'];
        }
        return $array;
    }



}
