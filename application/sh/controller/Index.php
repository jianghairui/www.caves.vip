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
    //资讯列表
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
    //添加资讯页面
    public function articleAdd() {
        $tag = Db::table('mp_tag')->select();
        $this->assign('tag',$tag);
        return $this->fetch();
    }
    //添加资讯提交
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
    //修改资讯页面
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
    //修改资讯提交
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
    //删除资讯
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
    //停用资讯
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
    //启用资讯
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

    //案例列表
    public function caseList()
    {
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [];
        if($param['search']) {
            $where[] = ['a.title|a.desc','like',"%{$param['search']}%"];
        }
        $count = Db::table('mp_case')->alias('a')->where($where)->count();

        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('mp_case')->alias('a')
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
    //添加案例页面
    public function caseAdd() {
        return $this->fetch();
    }
    //添加案例提交
    public function caseAddPost() {
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        $val['status'] = input('post.status');
        $this->checkPost($val);
        $val['content'] = input('post.content');
        $val['admin_id'] = session('admin_id');
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
            Db::table('mp_case')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([]);

    }
    //修改案例页面
    public function caseDetail() {
        $article_id = input('param.id');
        $exist = Db::table('mp_case')->where('id',$article_id)->find();
        if(!$exist) {
            $this->error('非法操作');
        }
        $this->assign('info',$exist);
        return $this->fetch();
    }
    //修改案例提交
    public function caseModPost() {
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        $val['status'] = input('post.status');
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $val['content'] = input('post.content');
        $val['admin_id'] = session('admin_id');
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
            $exist = Db::table('mp_case')->where('id',$val['id'])->find();
            $res = Db::table('mp_case')->update($val);
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
    //删除案例
    public function caseDel() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_case')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        $model = model('Mcase');
        try {
            $model::destroy($val['id']);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        return ajax([],1);
    }
    //停用案例
    public function caseHide() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_case')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_case')->where('id',$val['id'])->update(['status'=>0]);
        if($res !== false) {
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }
    //启用案例
    public function caseShow() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_case')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_case')->where('id',$val['id'])->update(['status'=>1]);
        if($res !== false) {
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }

    public function partnerList() {
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [];
        if($param['search']) {
            $where[] = ['title','like',"%{$param['search']}%"];
        }
        $count = Db::table('mp_partner')->where($where)->count();

        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('mp_partner')->where($where)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();

    }

    //添加合作伙伴页面
    public function partnerAdd() {
        return $this->fetch();
    }
    //添加合作伙伴提交
    public function partnerAddPost() {
        $val['title'] = input('post.title');
        $this->checkPost($val);
        $val['admin_id'] = session('admin_id');
        if(isset($_FILES['file'])) {
            $info = $this->upload('file');
            if($info['error'] === 0) {
                $val['pic'] = $info['data'];
            }else {
                return ajax($info['msg'],-1);
            }
        }else {
            return ajax('请上传图片',-1);
        }
        try {
            Db::table('mp_partner')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([]);

    }
    //修改合作伙伴页面
    public function partnerDetail() {
        $article_id = input('param.id');
        $exist = Db::table('mp_partner')->where('id',$article_id)->find();
        if(!$exist) {
            $this->error('非法操作');
        }
        $this->assign('info',$exist);
        return $this->fetch();
    }
    //修改合作伙伴提交
    public function partnerModPost() {
        $val['title'] = input('post.title');
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $val['admin_id'] = session('admin_id');
        if(isset($_FILES['file'])) {
            $info = $this->upload('file');
            if($info['error'] === 0) {
                $val['pic'] = $info['data'];
            }else {
                return ajax($info['msg'],-1);
            }
        }
        try {
            $exist = Db::table('mp_partner')->where('id',$val['id'])->find();
            Db::table('mp_partner')->update($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        if(isset($val['pic'])) {
            @unlink($exist['pic']);
        }
        return ajax();

    }
    //删除合作伙伴
    public function partnerDel() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_partner')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        $model = model('partner');
        try {
            $model::destroy($val['id']);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        return ajax([],1);
    }






}
