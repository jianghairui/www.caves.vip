<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/19
 * Time: 20:59
 */
namespace app\sh\controller;
use think\Db;
class Admin extends Common {

    public function adminlist() {
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];

        if($param['logmin']) {
            $where[] = ['a.create_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['logmin'])))];
        }

        if($param['logmax']) {
            $where[] = ['a.create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['logmax'])))];
        }

        if($param['search']) {
            $where[] = ['a.username|a.realname|a.tel','like',"%{$param['search']}%"];
        }

        $count = Db::table('mp_admin')->alias('a')->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $list = Db::table('mp_admin')->alias('a')
            ->join('mp_auth_group_access au','a.id=au.uid','left')
            ->join('mp_auth_group g','au.group_id=g.id','left')
            ->where($where)
            ->field('a.*,g.title')
            ->order(['a.id'=>'ASC'])
            ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function adminadd() {
        $list = Db::table('mp_auth_group')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function adminadd_post() {
        $data = input('post.');
        $group_id = $data['group_id'];
        if($group_id) {
            $g = Db::table('mp_auth_group')->where('id',$group_id)->find();
            if(!$g) {
                return ajax('用户组不存在',-1);
            }
        }
        if($data['tel']) {
            if(!is_tel($data['tel'])) {
                return ajax('手机号不合法',-1);
            }
        }
        unset($data['group_id']);
        unset($data['password2']);
        $data['password'] = md5($data['password'] . config('login_key'));

        $exist = Db::table('mp_admin')->where('username',$data['username'])->find();
        if($exist) {
            return ajax('用户名已存在',-1);
        }
        try {
            $data['create_time'] = time();
            Db::table('mp_admin')->insert($data);
            if($group_id) {
                $id = Db::getLastInsID();
                Db::table('mp_auth_group_access')->insert(['uid'=>$id,'group_id'=>$group_id]);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($data);
    }

    public function adminmod() {
        $id = input('param.id');
        if($id == 1) {
            return $this->fetch('public/noAuth');
        }
        $info = Db::table('mp_admin')->where('id',$id)->find();
        $group_id = Db::table('mp_auth_group_access')->where('uid',$id)->value('group_id');
        $list = Db::table('mp_auth_group')->select();
        $this->assign('list',$list);
        $this->assign('info',$info);
        $this->assign('group_id',$group_id);
        return $this->fetch();
    }

    public function adminmod_post() {
        $data = input('post.');
        $group_id = $data['group_id'];
        if($group_id) {
            $g = Db::table('mp_auth_group')->where('id',$group_id)->find();
            if(!$g) {
                return ajax('用户组不存在',-1);
            }
        }
        if($data['tel']) {
            if(!is_tel($data['tel'])) {
                return ajax('手机号不合法',-1);
            }
        }
        unset($data['group_id']);
        unset($data['password2']);
        if($data['password']) {
            $data['password'] = md5($data['password'] . config('login_key'));
        }else {
            unset($data['password']);
        }

        $map = [
            ['username','=',$data['username']],
            ['id','<>',$data['id']],
        ];
        $exist = Db::table('mp_admin')->where($map)->find();
        if($exist) {
            return ajax('用户名已存在',-1);
        }
        try {
            Db::table('mp_admin')->where('id',$data['id'])->update($data);
            $res =  Db::table('mp_auth_group_access')->where('uid',$data['id'])->find();
            if($group_id) {
                if($res) {
                    Db::table('mp_auth_group_access')->where('uid',$data['id'])->update(['group_id'=>$group_id]);
                }else {
                    Db::table('mp_auth_group_access')->insert(['uid'=>$data['id'],'group_id'=>$group_id]);
                }
            }else {
                Db::table('mp_auth_group_access')->where('uid',$data['id'])->delete();
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($data);
    }

    public function adminDel() {
        $id = input('post.id');
        if($id == 1) {
            return ajax('没有权限',-1);
        }
        try {
            Db::table('mp_admin')->where('id','=',$id)->delete();
            Db::table('mp_auth_group_access')->where('uid','=',$id)->delete();
            Db::table('mp_syslog')->where('admin_id','=',$id)->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function admin_multidel() {
        $ids = input('post.check');
        if(empty($ids)) {
            return ajax('未选择删除项',-1);
        }
        $map1 = [
            ['id','in',$ids],
            ['id','<>',1]
        ];
        $map2 = [
            ['uid','in',$ids],
            ['uid','<>',1]
        ];
        $map3 = [
            ['admin_id','in',$ids],
            ['admin_id','<>',1]
        ];
        try {
            Db::table('mp_admin')->where($map1)->delete();
            Db::table('mp_auth_group_access')->where($map2)->delete();
            Db::table('mp_syslog')->where($map3)->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function adminStop() {
        $id = input('post.id');
        if($id == 1) {
            return ajax('没有权限',-1);
        }
        try {
            Db::table('mp_admin')->where('id','=',$id)->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function adminStart() {
        $id = input('post.id');
        if($id == 1) {
            return ajax('没有权限',-1);
        }
        try {
            Db::table('mp_admin')->where('id','=',$id)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function rulelist() {
        $list = Db::table('mp_auth_rule')->where('status',1)->select();
        $newlist = $this->sortMerge($list);
        $this->assign('list',$newlist);
        return $this->fetch();
    }

    public function ruleadd() {
        $pid = input('param.pid');
        if($pid) {
            $map = [
                ['id','=',$pid],
                ['pid','=',0]
            ];
            $res = Db::table('mp_auth_rule')->where($map)->find();
            if($res) {
                $pname = $res['name'];
            }else {
                $pid = 0;
                $pname = '顶级节点';
            }
        }else {
            $pid = 0;
            $pname = '顶级节点';
        }
        $this->assign('pid',$pid);
        $this->assign('pname',$pname);
        return $this->fetch();
    }

    public function ruleadd_post() {
        $val['name'] = input('post.name');
        $val['title'] = input('post.title');
        $val['pid'] = input('post.pid');
        if($val['pid']) {
            $res = Db::table('mp_auth_rule')->where('id',$val['pid'])->find();
            if(!$res) {
                return ajax('非法参数',-1);
            }
        }
        try {
            Db::table('mp_auth_rule')->insert($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val,1);
    }

    public function ruledel() {
        $rules = input('post.check');
        if(empty($rules)) {
            return ajax('未选择删除项',-1);
        }
        $map = [
            ['pid','=',0],
            ['id','in',$rules]
        ];
        $arr = Db::table('mp_auth_rule')->where($map)->column('id');
        try{
            if($arr) {
                Db::table('mp_auth_rule')->where('pid','in',$arr)->delete();
            }
            Db::table('mp_auth_rule')->where('id','in',$rules)->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function grouplist() {
        $list = Db::table('mp_auth_group')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function groupadd() {
        $list = Db::table('mp_auth_rule')->where('status',1)->select();
        $newlist = $this->sortMerge($list);
        $this->assign('list',$newlist);
        return $this->fetch();
    }

    public function groupadd_post() {
        $data['title'] = input('post.title');
        $this->checkPost($data);
        $data['desc'] = input('post.desc');
        $check = input('post.check');
        $exist = Db::table('mp_auth_group')->where('title','=',$data['title'])->find();
        if($exist) {
            return ajax('角色已存在',-1);
        }
        if($check && is_array($check)) {
            $count = Db::table('mp_auth_rule')->where('id','in',$check)->count();
            if($count !== count($check)) {
                return ajax('非法参数',-1);
            }
            $data['rules'] = implode(',',$check);
        }
        try {
            Db::table('mp_auth_group')->insert($data);
        }catch (\Exception $e) {
            return ajax($e->getMessage());
        }
        return ajax($data);
    }

    public function groupmod() {
        $gid = input('param.gid');
        $exist = Db::table('mp_auth_group')->where('id','=',$gid)->find();
        if(!$exist) {
            $this->error('非法操作');
        }
        $list = Db::table('mp_auth_rule')->where('status',1)->select();
        $rules = Db::table('mp_auth_group')->where('id','=',$gid)->value('rules');
        $access = explode(',',$rules);
        $newlist = $this->sortMerge($list,$access);
        $this->assign('list',$newlist);
        $this->assign('info',$exist);
        return $this->fetch();
    }

    public function groupmod_post() {
        $data['title'] = input('post.title');
        $data['id'] = input('post.group_id');
        $this->checkPost($data);

        $data['desc'] = input('post.desc');
        $check = input('post.check');
        $map = [
            ['title','=',$data['title']],
            ['id','<>',$data['id']]
        ];

        $exist = Db::table('mp_auth_group')->where($map)->find();
        if($exist) {
            return ajax('角色已存在',-1);
        }
        if($check && is_array($check)) {
            $count = Db::table('mp_auth_rule')->where('id','in',$check)->count();
            if($count !== count($check)) {
                return ajax('非法参数',-1);
            }
            $data['rules'] = implode(',',$check);
        }else {
            $data['rules'] = '';
        }
        try {
            Db::table('mp_auth_group')->where('id','=',$data['id'])->update($data);
        }catch (\Exception $e) {
            return ajax($e->getMessage());
        }
        return ajax([]);
    }

    public function groupdel() {
        $gid = input('post.gid');
        try{
            Db::table('mp_auth_group')->where('id','=',$gid)->delete();
            Db::table('mp_auth_group_access')->where('group_id','=',$gid)->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function group_multidel() {
        $gid = input('post.check');
        if(empty($gid)) {
            return ajax('未选择删除项',-1);
        }
        try{
            Db::table('mp_auth_group')->where('id','in',$gid)->delete();
            Db::table('mp_auth_group_access')->where('group_id','in',$gid)->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    private function sortMerge($node,$access=null,$pid=0)
    {
        $arr = array();
        foreach($node as $key=>$v)
        {
            if(is_array($access))
            {
                $v['access'] = in_array($v['id'],$access) ? 1 : 0;
            }
            if($v['pid'] == $pid)
            {
                $v['child'] = $this->sortMerge($node,$access,$v['id']);
                $arr[] = $v;
            }
        }
        return $arr;
    }



}