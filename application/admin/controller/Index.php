<?php
namespace app\admin\controller;
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
    //查看需求列表
    public function rlist() {
        $param['status'] = input('param.status','');
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',config('app.page'));
        $perpage = input('param.perpage',2);

        $where = [
            ['r.del','=',0]
        ];

        if(!is_null($param['status']) && $param['status'] !== '') {
            $where[] = ['r.status','=',$param['status']];
        }

        if($param['logmin']) {
            $where[] = ['r.create_time','>=',$param['logmin']];
        }

        if($param['logmax']) {
            $where[] = ['r.create_time','<=',$param['logmax']];
        }

        if($param['search']) {
            $where[] = ['r.title|r.org','like',"%{$param['search']}%"];
        }

        $count = Db::table('mp_req')->alias('r')
            ->join('mp_role ro','r.uid=ro.uid','left')
            ->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $list = Db::table('mp_req')->alias('r')
            ->join('mp_role ro','r.uid=ro.uid','left')
            ->field('r.*,ro.org')
            ->where($where)->order(['r.id'=>'DESC'])->limit(($curr_page - 1)*$perpage,$perpage)->select();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('status',$param['status']);
        return $this->fetch();
    }
    //查看需求详情
    public function reqDetail() {
        $rid = input('param.rid');
        try {
            $info = Db::table('mp_req')
                ->where('id','=',$rid)
                ->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $this->assign('info',$info);
        return $this->fetch();
    }
    //需求审核-通过
    public function reqPass() {
        $map = [
            ['status','=',0],
            ['id','=',input('post.id',0)]
        ];
        try {
            $exist = Db::table('mp_req')->where($map)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_req')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //需求审核-拒绝
    public function reqReject() {
        $map = [
            ['status','=',0],
            ['id','=',input('post.id',0)]
        ];
        $reason = input('post.reason','');
        try {
            $exist = Db::table('mp_req')->where($map)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_req')->where($map)->update(['status'=>2,'reason'=>$reason]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //删除需求
    public function reqDel() {
        $map = [
            ['id','=',input('post.id',0)]
        ];
        try {
            $exist = Db::table('mp_req')->where($map)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_req')->where($map)->update(['del'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    public function reqShow() {
        $map[] = ['id','=',input('post.id',0)];
        try {
            Db::table('mp_req')->where($map)->update(['show'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    public function reqHide() {
        $map[] = ['id','=',input('post.id',0)];
        try {
            Db::table('mp_req')->where($map)->update(['show'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }


}
