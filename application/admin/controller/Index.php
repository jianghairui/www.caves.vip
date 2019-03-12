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
        $perpage = input('param.perpage',10);

        $where = [];

        if(!is_null($param['status']) && $param['status'] !== '') {
            $where[] = ['r.status','=',$param['status']];
        }

        if($param['logmin']) {
            $where[] = ['r.create_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['logmin'])))];
        }

        if($param['logmax']) {
            $where[] = ['r.create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['logmax'])))];
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
    public function detail() {
        $rid = input('param.rid');
        $info = Db::table('mp_req')->alias('r')
            ->join('mp_cate c','r.cate_id=c.id','left')
            ->where('r.id','=',$rid)
            ->field('r.*,c.cate_name')
            ->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    //需求审核-通过
    public function reqPass() {
        $map[] = ['status','=',0];
        $map[] = ['pay_status','=',1];
        $map[] = ['id','=',input('post.id',0)];

        $exist = Db::table('mp_req')->where($map)->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        try {
            Db::table('mp_req')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //需求审核-拒绝
    public function reqReject() {
        $map[] = ['status','=',0];
        $map[] = ['pay_status','=',1];
        $map[] = ['id','=',input('post.id',0)];

        $exist = Db::table('mp_req')->where($map)->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        try {
            Db::table('mp_req')->where($map)->update(['status'=>-1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        //todo 退款
        $arg = [
            'order_sn' => $exist['order_sn'],
            'reason' => '需求未通过审核'
        ];
        $this->asyn_refund($arg);
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
