<?php
namespace app\sh\controller;
use my\Auth;
use think\Db;
use think\Exception;
use EasyWeChat\Factory;

class Museum extends Common
{
    //博物馆列表
    public function museumList()
    {
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [];
        if($param['search']) {
            $where[] = ['a.title|a.desc','like',"%{$param['search']}%"];
        }
        $count = Db::table('mp_museum')->alias('a')->where($where)->count();

        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('mp_museum')->alias('a')
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
    //添加博物馆页面
    public function museumAdd() {
        return $this->fetch();
    }
    //添加博物馆提交
    public function museumAddPost() {
        $val['title'] = input('post.title');
        $val['status'] = input('post.status');
        $val['lon'] = input('post.lon');
        $val['lat'] = input('post.lat');
        $val['ticket_price'] = input('post.ticket_price');
        $val['address'] = input('post.address');
        $val['hours'] = input('post.hours');
        $this->checkPost($val);
        $val['desc'] = input('post.desc');
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
            Db::table('mp_museum')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([]);

    }
    //修改博物馆页面
    public function museumDetail() {
        $museum_id = input('param.id');
        $exist = Db::table('mp_museum')->where('id',$museum_id)->find();
        if(!$exist) {
            $this->error('非法操作');
        }
        $this->assign('info',$exist);
        return $this->fetch();
    }
    //修改博物馆提交
    public function museumModPost() {
        $val['title'] = input('post.title');
        $val['status'] = input('post.status');
        $val['lon'] = input('post.lon');
        $val['lat'] = input('post.lat');
        $val['ticket_price'] = input('post.ticket_price');
        $val['address'] = input('post.address');
        $val['hours'] = input('post.hours');
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $val['desc'] = input('post.desc');
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
            $exist = Db::table('mp_museum')->where('id',$val['id'])->find();
            $res = Db::table('mp_museum')->update($val);
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
    //删除博物馆
    public function museumDel() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_museum')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        $model = model('museum');
        try {
            $model::destroy($val['id']);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        return ajax([],1);
    }
    //停用博物馆
    public function museumHide() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_museum')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_museum')->where('id',$val['id'])->update(['status'=>0]);
        if($res !== false) {
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }
    //启用博物馆
    public function museumShow() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_museum')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_museum')->where('id',$val['id'])->update(['status'=>1]);
        if($res !== false) {
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }

    public function collectionList() {
        $param['search'] = input('param.search');
        $param['mid'] = input('param.mid','');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',5);
        $where = [];
        if($param['search']) {
            $where[] = ['c.title|c.desc','like',"%{$param['search']}%"];
        }
        if($param['mid']) {
            $where[] = ['c.mid','=',$param['mid']];
        }
        $count = Db::table('mp_collection')->alias('c')
            ->join('mp_museum m','c.mid=m.id','left')
            ->where($where)->count();

        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('mp_collection')->alias('c')
                ->join('mp_admin a','c.admin_id=a.id','left')
                ->join('mp_museum m','c.mid=m.id','left')
                ->field('c.*,a.realname,m.title as museum')
                ->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $mlist = Db::table('mp_museum')->select();

        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('mlist',$mlist);
        $this->assign('mid',$param['mid']);
        return $this->fetch();
    }

    public function collectionAdd() {
        $list = Db::table('mp_museum')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function collectionAddPost() {
        $val['title'] = input('post.title');
        $val['mid'] = input('post.mid');
        $this->checkPost($val);
        $val['url'] = input('post.url');
        $val['desc'] = input('post.desc');
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
            Db::table('mp_collection')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);

    }

    public function collectionDetail() {
        $id = input('param.id');
        $info = Db::table('mp_collection')->where('id',$id)->find();
        $list = Db::table('mp_museum')->select();
        $this->assign('info',$info);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function collectionModPost() {
        $val['title'] = input('post.title');
        $val['mid'] = input('post.mid');
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $val['url'] = input('post.url');
        $val['desc'] = input('post.desc');
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
            $exist = Db::table('mp_collection')->where('id',$val['id'])->find();
            Db::table('mp_collection')->update($val);
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

    //删除博物馆
    public function collectionDel() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_collection')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        $model = model('collection');
        try {
            $model::destroy($val['id']);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        return ajax([],1);
    }
    //停用博物馆
    public function collectionHide() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_collection')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_collection')->where('id',$val['id'])->update(['status'=>0]);
        if($res !== false) {
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }
    //启用博物馆
    public function collectionShow() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_collection')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_collection')->where('id',$val['id'])->update(['status'=>1]);
        if($res !== false) {
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }


}
