<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/1/9
 * Time: 10:57
 */
namespace app\admin\controller;
use think\Db;
class User extends Common {

    public function userList() {
        $param['role'] = input('param.role','');
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];

        if(!is_null($param['role']) && $param['role'] !== '') {
            $where[] = ['role','=',$param['role']];
        }

        if($param['logmin']) {
            $where[] = ['created_at','>=',date('Y-m-d 00:00:00',strtotime($param['logmin']))];
        }

        if($param['logmax']) {
            $where[] = ['created_at','<=',date('Y-m-d 23:59:59',strtotime($param['logmax']))];
        }

        if($param['search']) {
            $where[] = ['nickname|name|mobile','like',"%{$param['search']}%"];
        }

        $count = Db::table('user')->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('user')->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('role',$param['role']);
        return $this->fetch();
    }

    public function userDetail() {
        $id = input('param.id');
        try {
            $info = Db::table('user')->where('id',$id)->find();
        }catch (\Exception $e) {
            exit($e->getMessage());
        }
        $this->assign('info',$info);
        return $this->fetch();
    }

//拉黑用户
    public function userStop() {
        $id = input('post.id');
        $map[] = ['id','=',$id];
        try {
            $res = Db::table('user')->where($map)->update(['status'=>2]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax([],1);
        }else {
            return ajax('拉黑失败',-1);
        }
    }
    //恢复用户
    public function userGetback() {
        $id = input('post.id');
        $map[] = ['status','=',2];
        $map[] = ['id','=',$id];
        try {
            $res = Db::table('user')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax([],1);
        }else {
            return ajax('恢复失败',-1);
        }
    }

    public function multiStop() {
        $map[] = ['status','<>',2];
        $id_array = input('post.check');
        if(empty($id_array)) {
            return ajax('请选择拉黑对象',-1);
        }
        $map[] = ['id','in',$id_array];

        try {
            $res = Db::table('user')->where($map)->update(['status'=>2]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax('共拉黑' . $res . '个用户',1);
    }


}