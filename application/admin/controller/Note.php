<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/1/9
 * Time: 11:09
 */
namespace  app\admin\controller;
use think\Db;
class Note extends Common {

    public function noteList() {

        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];

        if($param['logmin']) {
            $where[] = ['n.created_at','>=',date('Y-m-d 00:00:00',strtotime($param['logmin']))];
        }

        if($param['logmax']) {
            $where[] = ['n.created_at','<=',date('Y-m-d 23:59:59',strtotime($param['logmax']))];
        }

        if($param['search']) {
            $where[] = ['n.title|n.content','like',"%{$param['search']}%"];
        }

        $count = Db::table('note')->alias("n")->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        try {
            $list = Db::table('note')->alias('n')
                ->join("user u","n.uid=u.id","left")
                ->join("goods g","n.goods_id=g.id","left")
                ->field("n.*,u.nickname,g.goods_name")
                ->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }

        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function notePass() {
        $map[] = ['status','=',0];
        $map[] = ['id','=',input('post.id',0)];

        $exist = Db::table('note')->where($map)->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        try {
            Db::table('note')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    public function noteReject() {
        $map[] = ['status','=',0];
        $map[] = ['id','=',input('post.id',0)];
        $reason = input('post.reason','');

        $exist = Db::table('note')->where($map)->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        try {
            Db::table('note')->where($map)->update(['status'=>2,'reason'=>$reason]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    public function noteDetail() {
        $id = input('param.id');
        try {
            $info = Db::table('note')->alias("n")
                ->join("user u","n.uid=u.id","left")
                ->join("goods g","n.goods_id=g.id","left")
                ->field("n.*,u.nickname,g.goods_name")
                ->where('n.id','=',$id)->find();
        }catch (\Exception $e) {
            die('参数无效');
        }
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function noteDel() {
        $map[] = ['id','=',input('post.id',0)];
        try {
            Db::table('note')->where($map)->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

}