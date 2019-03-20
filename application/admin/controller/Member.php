<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/25
 * Time: 16:09
 */
namespace app\admin\controller;
use think\Db;
use think\Exception;
use EasyWeChat\Factory;
use think\exception\HttpResponseException;

class Member extends Common {

    public function initialize() {
        parent::initialize(); // TODO: Change the autogenerated stub
    }
    //会员列表
    public function memberlist() {
        $param['status'] = input('param.status','');
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];

        if(!is_null($param['status']) && $param['status'] !== '') {
            $where[] = ['status','=',$param['status']];
        }

        if($param['logmin']) {
            $where[] = ['create_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['logmin'])))];
        }

        if($param['logmax']) {
            $where[] = ['create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['logmax'])))];
        }

        if($param['search']) {
            $where[] = ['nickname|tel','like',"%{$param['search']}%"];
        }

        $count = Db::table('mp_user')->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $list = Db::table('mp_user')->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('status',$param['status']);
        return $this->fetch();
    }
    //申请角色-通过审核
    public function rolePass() {
        $map = [
            ['auth','=',1],
            ['id','=',input('post.id',0)]
        ];
        try {
            $exist = Db::table('mp_user')->where($map)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_user')->where($map)->update(['auth'=>2]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //申请角色-拒绝审核
    public function roleReject() {
        $map = [
            ['auth','=',1],
            ['id','=',input('post.id',0)]
        ];
        $reason = input('post.reason','');
        try {
            $exist = Db::table('mp_user')->where($map)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_user')->where($map)->update(['auth'=>3]);
            Db::table('mp_role')->where(['uid'=>1])->update(['reason'=>$reason]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //用户详情
    public function detail() {
        $id = input('param.id');
        $where = [
            ['id','=',$id]
        ];
        try {
            $info = Db::table('mp_user')->alias('u')
                ->join('mp_role r','u.id=r.uid','LEFT')
                ->field('u.*,r.name,r.identity,r.id_front,r.id_back,r.tel as role_tel,r.weixin,r.works,r.license')
                ->where($where)
                ->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $this->assign('info',$info);
        return $this->fetch();
    }
    //充值类目列表
    public function vipList() {
        $where = [];
        $where[] = ['status','=',1];
        try {
            $list = Db::table('mp_vip')->where($where)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    //添加充值类目
    public function vipAdd() {
        return $this->fetch();
    }
    //添加充值类目POST
    public function vipAddPost() {
        $val['title'] = input('post.title');
        $val['price'] = input('post.price');
        $val['detail'] = input('post.detail');
        $val['days'] = input('post.days');
        $this->checkPost($val);
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
            Db::table('mp_vip')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);

    }
    //充值类目详情
    public function vipDetail() {
        $id = input('param.id');
        $info = Db::table('mp_vip')->where('id',$id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    //充值类目编辑
    public function vipModPost() {
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
            $exist = Db::table('mp_vip')->where('id',$val['id'])->find();
            Db::table('mp_vip')->update($val);
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
    //删除会员
    public function vipDel() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $exist = Db::table('mp_vip')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        $model = model('vip');
        try {
            $model::destroy($val['id']);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    public function rechargeList() {
        $param['status'] = input('param.status','');
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];
        try {
            $count = Db::table('mp_vip_order')->alias('o')->where($where)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_vip_order')->alias('o')
                ->join("mp_user u","o.uid=u.id","left")
                ->join("mp_vip v","o.vip_id=v.id","left")
                ->field("o.*,u.nickname,u.avatar,v.title")
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }



}