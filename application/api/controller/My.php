<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/11
 * Time: 16:00
 */
namespace app\api\controller;

use think\Db;
class My extends Common {

    public function mydetail() {
        $map = [
            ['id','=',$this->myinfo['uid']]
        ];
        try {
            $info = Db::table('mp_user')->where($map)->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($info);
    }

    public function getMyNoteList()
    {
        $page = input('page',1);
        $perpage = input('perpage',10);
        $where = [
            ['n.uid','=',$this->myinfo['uid']]
        ];
        try {
            $ret['count'] = Db::table('mp_note')->alias('n')->where($where)->count();
            $list = Db::table('mp_note')->alias('n')
                ->join('mp_user u','n.uid=u.id','left')
                ->where($where)
                ->field('n.id,n.title,n.pics,u.nickname,n.like,u.avatar')
                ->order(['n.create_time'=>'DESC'])
                ->limit(($page-1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
        }
        $ret['list'] = $list;
        return ajax($ret);
    }

    public function getMyCollectedNoteList() {
        $page = input('page',1);
        $perpage = input('perpage',10);
        $where = [
            ['n.uid','=',$this->myinfo['uid']]
        ];
        try {
            $ret['count'] = Db::table('mp_collect')->alias('c')->where($where)->count();
            $list = Db::table('mp_collect')->alias('c')
                ->join('mp_note n','c.note_id=n.id','left')
                ->join('mp_user u','n.uid=u.id','left')
                ->where($where)
                ->field('n.id,n.title,n.pics,u.nickname,n.like,u.avatar')
                ->order(['n.create_time'=>'DESC'])
                ->limit(($page-1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
        }
        $ret['list'] = $list;
        return ajax($ret);
    }


    public function apply() {
        $val['title'] = input('post.title');
        $val['org'] = input('post.org');
        $val['explain'] = input('post.explain');
        $val['theme'] = input('post.theme');
        $val['part_obj'] = input('post.part_obj');
        $val['part_way'] = input('post.part_way');
        $val['linkman'] = input('post.linkman');
        $val['tel'] = input('post.tel');
        $val['weixin'] = input('post.weixin');
        $val['start_time'] = input('post.start_time');
        $val['deadline'] = input('post.deadline');
        $val['vote_time'] = input('post.vote_time');
        $val['end_time'] = input('post.end_time');

        $this->checkPost($val);
        $image = input('post.cover','');
        if($image) {
            if(!file_exists($image)) {
                return ajax($image,5);
            }
            $val['cover'] = $this->rename_file($image);
        }else {
            return ajax('请传入图片',3);
        }
        try {
            Db::table('mp_req')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['cover'])) {
                @unlink($val['cover']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax();

    }

}