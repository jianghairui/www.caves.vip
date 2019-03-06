<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/11/30
 * Time: 14:07
 */
namespace app\admin\controller;

use think\Db;

class Other extends Common {

    public function joblist() {
        $list = Db::table('mp_job')->select();
        $count = count($list);

        $this->assign('list',$list);
        $this->assign('count',$count);
        return $this->fetch();
    }

    public function jobadd() {
        return $this->fetch();
    }

    public function jobadd_post() {
        $val['name'] = input('post.name');
        $val['create_time'] = time();
        try {
            Db::table('mp_job')->insert($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function jobdel() {
        $id = input('post.jid');
        try {
            Db::table('mp_job')->where('id',$id)->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function resumelist() {
        $list = Db::table('mp_resume')->select();
        $count = count($list);

        $this->assign('list',$list);
        $this->assign('count',$count);
        return $this->fetch();
    }

    public function resumeadd() {
        return $this->fetch();
    }

    public function resumeadd_post() {
        $val['content'] = input('post.content');
        $val['create_time'] = time();
        try {
            Db::table('mp_resume')->insert($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function resumedel() {
        $id = input('post.rid');
        try {
            Db::table('mp_resume')->where('id',$id)->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

}