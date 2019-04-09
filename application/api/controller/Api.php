<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/8
 * Time: 11:11
 */

namespace app\api\controller;

use think\Db;
use think\Exception;

class Api extends Common
{
//获取轮播图列表
    public function slideList()
    {
        try {
            $list = Db::table('mp_slideshow')->where([
                ['status', '=', 1]
            ])->order(['sort' => 'ASC'])->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }
//获取活动列表
    public function getReqList()
    {
        $type = input('post.type', '');
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        if (!in_array($type, [1, 2])) {
            return ajax($type, -4);
        }
        $where = [];
        if ($type == 1) {
            $where = [
                ['r.vote_time', '>', date('Y-m-d H:i:s')],
                ['r.status', '=', 1],
                ['r.show', '=', 1],
                ['r.del', '=', 0]
            ];
        }
        if ($type == 2) {
            $where = [
                ['r.vote_time', '<=', date('Y-m-d H:i:s')],
                ['r.end_time', '>', date('Y-m-d H:i:s')],
                ['r.status', '=', 1],
                ['r.show', '=', 1],
                ['r.del', '=', 0]
            ];
        }

        try {
            $list = Db::table('mp_req')
                ->alias('r')
                ->join("mp_user u", "r.uid=u.id", "left")
                ->where($where)->order(['r.start_time' => 'ASC'])
                ->field("r.id,r.title,r.cover,r.part_num,r.start_time,r.end_time,u.org as user_org")
                ->limit(($curr_page - 1) * $perpage, $perpage)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['start_time'] = date('Y-m-d', strtotime($v['start_time']));
            $v['end_time'] = date('Y-m-d', strtotime($v['end_time']));
        }
        return ajax($list);
    }
//获取首页活动标题列表
    public function getActiveList() {
        $where = [
            ['r.status', '=', 1],
            ['r.show', '=', 1],
            ['r.del', '=', 0],
            ['r.recommend', '=', 1]
        ];
        try {
            $list = Db::table('mp_req')
                ->alias('r')
                ->join("mp_user u", "r.uid=u.id", "left")
                ->where($where)->order(['r.start_time' => 'ASC'])
                ->field("r.id,r.title,r.pic,r.cover,r.part_num,r.start_time,r.end_time,u.org as user_org")
                ->limit(0,3)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['start_time'] = date('Y-m-d', strtotime($v['start_time']));
            $v['end_time'] = date('Y-m-d', strtotime($v['end_time']));
        }
        return ajax($list);
    }
//获取活动详情
    public function getReqDetail()
    {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $where = [
                ['r.status', '=', 1],
                ['r.show', '=', 1],
                ['r.del', '=', 0],
                ['r.id', '=', $val['id']],
            ];
            $info = Db::table('mp_req')->alias('r')
                ->join("mp_user u", "r.uid=u.id", "left")
                ->where($where)
                ->field("r.*,u.org as user_org")
                ->find();
            if (!$info) {
                return ajax($val['id'], -4);
            }
            if (date('Y-m-d 23:59:59', strtotime($info['end_time'])) <= date('Y-m-d H:i:s')) {
                return ajax('活动已结束,无法查看', 25);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        if ($info['start_time'] <= date('Y-m-d H:i:s') && $info['deadline'] > date('Y-m-d H:i:s')) {
            $info['join_btn'] = true;
        } else {
            $info['join_btn'] = false;
        }
        if ($info['deadline'] <= date('Y-m-d H:i:s') && $info['vote_time'] > date('Y-m-d H:i:s')) {
            $info['vote_btn'] = true;
        } else {
            $info['vote_btn'] = false;
        }
        if ($info['vote_time'] <= date('Y-m-d H:i:s') && $info['end_time'] > date('Y-m-d H:i:s')) {
            $info['bidding_btn'] = true;
        } else {
            $info['bidding_btn'] = false;
        }
        return ajax($info);
    }
//竞标
    public function bidding()
    {
        $val['work_id'] = input('post.work_id');
        $val['desc'] = input('post.desc');
        $this->checkPost($val);
        $user = $this->getMyInfo();
        $val['uid'] = $user['id'];
        try {
            $bidding_exist = Db::table('mp_bidding')->where([
                ['work_id', '=', $val['work_id']],
                ['uid', '=', $val['uid']]
            ])->find();
            if ($bidding_exist) {
                return ajax('已经参与竞标', 37);
            }
            $where_work = [
                ['id', '=', $val['work_id']]
            ];
            $work_exist = Db::table('mp_design_works')->where($where_work)->find();
            if (!$work_exist || $work_exist['type'] != 2) {
                return ajax($val['work_id'], -4);
            }
            if ($user['role'] != 4) {
                return ajax('只有工厂可以参加竞标', 35);
            }
            if ($user['auth'] != 2) {
                return ajax('角色未认证', 29);
            }
            $where_req = [
                ['id', '=', $work_exist['req_id']]
            ];
            $req_exist = Db::table('mp_req')->where($where_req)->find();
            $val['req_id'] = $req_exist['id'];
            if ($req_exist['end_time'] <= date('Y-m-d H:i:s')) {
                return ajax('活动已结束', 25);
            }
            if (!($req_exist['vote_time'] <= date('Y-m-d H:i:s') && $req_exist['end_time'] > date('Y-m-d H:i:s'))) {
                return ajax('当前时间段无法竞标', 36);
            }
            Db::table('mp_bidding')->insert($val);
            Db::table('mp_design_works')->where($where_work)->setInc('bid_num');
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
//我要参加
    public function takePartIn()
    {
        $val['req_id'] = input('post.req_id');
        $this->checkPost($val);
        $user = $this->getMyInfo();
        try {
            $where = [
                ['id', '=', $val['req_id']]
            ];
            $exist = Db::table('mp_req')->where($where)->find();
            if (!$exist) {
                return ajax('非法参数', -4);
            }
            if ($exist['start_time'] > date('Y-m-d H:i:s')) {
                return ajax('活动未开始', 26);
            }
            if ($exist['end_time'] <= date('Y-m-d H:i:s')) {
                return ajax('活动已结束', 25);
            }
            if ($exist['deadline'] <= date('Y-m-d H:i:s')) {
                return ajax('报名时间已结束', 27);
            }
            if ($user['role'] != 3) {
                return ajax('只有设计师可以参加', 28);
            }
            if ($user['auth'] != 2) {
                return ajax('角色未认证', 29);
            }
            $where = [
                ['req_id', '=', $val['req_id']],
                ['type', '=', 2],
                ['uid', '=', $this->myinfo['uid']]
            ];
            $workExist = Db::table('mp_design_works')->where($where)->find();
            if ($workExist) {
                return ajax('已参加', 31);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
//上传参赛作品
    public function uploadWorks()
    {
        $val['req_id'] = input('post.req_id');
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        $this->checkPost($val);
        $user = $this->getMyInfo();
        $val['uid'] = $this->myinfo['uid'];
        $val['type'] = 2;
        $val['create_time'] = time();
        $image = input('post.pics', []);
        if (is_array($image) && !empty($image)) {
            if (count($image) > 9) {
                return ajax('最多上传9张图片', 8);
            }
            foreach ($image as $v) {
                if (!file_exists($v)) {
                    return ajax($v, 5);
                }
            }
        } else {
            return ajax('请传入图片', 3);
        }
        try {
            $where = [
                ['req_id', '=', $val['req_id']],
                ['type', '=', 2],
                ['uid', '=', $this->myinfo['uid']]
            ];
            $workExist = Db::table('mp_design_works')->where($where)->find();
            if ($workExist) {
                return ajax('已参加', 31);
            }
            $where = [
                ['id', '=', $val['req_id']]
            ];
            $exist = Db::table('mp_req')->where($where)->find();
            if (!$exist) {
                return ajax('非法参数', -4);
            }
            if ($exist['start_time'] > date('Y-m-d H:i:s')) {
                return ajax('活动未开始', 26);
            }
            if ($exist['end_time'] <= date('Y-m-d H:i:s')) {
                return ajax('活动已结束', 25);
            }
            if ($exist['deadline'] <= date('Y-m-d H:i:s')) {
                return ajax('投稿时间已结束', 27);
            }
            if ($user['role'] != 3) {
                return ajax('只有设计师可以参加', 28);
            }
            if ($user['auth'] != 2) {
                return ajax('角色还未认证', 29);
            }
            $image_array = [];
            foreach ($image as $v) {
                $image_array[] = $this->rename_file($v, 'static/uploads/work/');
            }
            $val['pics'] = serialize($image_array);
            $where = [
                ['req_id', '=', $val['req_id']],
                ['type', '=', 2],
                ['uid', '=', $this->myinfo['uid']]
            ];
            $workExist = Db::table('mp_design_works')->where($where)->find();
            if ($workExist) {
                foreach ($image_array as $v) {
                    @unlink($v);
                }
                return ajax('已参加', 31);
            }
            Db::table('mp_design_works')->insert($val);
            Db::table('mp_req')->where('id', $val['req_id'])->setInc('part_num');
        } catch (\Exception $e) {
            foreach ($image_array as $v) {
                @unlink($v);
            }
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
//获取参赛作品列表
    public function worksList()
    {
        $val['req_id'] = input('post.req_id');
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        $this->checkPost($val);
        try {
            $where = [
                ['w.req_id', '=', $val['req_id']]
            ];
            $list = Db::table('mp_design_works')->alias('w')
                ->join("mp_req r", "w.req_id=r.id", "left")
                ->join("mp_user u", "w.uid=u.id", "left")
                ->where($where)
                ->field("w.id,w.title,w.vote,w.bid_num,w.pics,u.nickname,u.avatar")
                ->limit(($curr_page - 1) * $perpage, $perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['cover'] = unserialize($v['pics'])[0];
            unset($v['pics']);
        }
        return ajax($list);
    }
//参赛作品详情
    public function worksDetail()
    {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $exist = Db::table('mp_design_works')->alias('w')
                ->join("mp_user u", "w.uid=u.id", "left")
                ->join("mp_role r", "w.uid=r.uid", "left")
                ->where('w.id', $val['id'])
                ->field("w.id,w.title,w.desc,w.pics,w.type,u.avatar,u.nickname,r.name")
                ->find();
            if (!$exist) {
                return ajax($val['id'], -4);
            }
            $bidding_exist = Db::table('mp_bidding')->where([
                ['work_id', '=', $val['id']],
                ['uid', '=', $this->myinfo['uid']]
            ])->find();
            if ($bidding_exist) {
                $exist['bidding'] = true;
            } else {
                $exist['bidding'] = false;
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $exist['pics'] = unserialize($exist['pics']);
        return ajax($exist);
    }
//获取竞标列表
    public function biddingList()
    {
        $val['work_id'] = input('post.work_id');
        $this->checkPost($val);
        try {
            $where = [
                ['b.work_id', '=', $val['work_id']]
            ];
            $list = Db::table('mp_bidding')->alias("b")
                ->join("mp_user u", "b.uid=u.id", "left")
                ->field("b.*,u.nickname,u.avatar")
                ->where($where)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }
//投票
    public function vote()
    {
        $val['work_id'] = input('post.work_id');
        $this->checkPost($val);
        $user = $this->getMyInfo();
        try {
            $whereVote = [
                ['work_id', '=', $val['work_id']],
                ['uid', '=', $this->myinfo['uid']]
            ];
            $vote_exist = Db::table('mp_vote')->where($whereVote)->find();
            if ($vote_exist) {
                return ajax('已投票', 32);
            }
            $where = [
                ['id', '=', $val['work_id']],
                ['type', '=', 2]
            ];
            $workExist = Db::table('mp_design_works')->where($where)->find();
            if (!$workExist) {
                return ajax($val['work_id'], -4);
            }
            $map = [
                ['id', '=', $workExist['req_id']]
            ];
            $exist = Db::table('mp_req')->where($map)->find();
            if ($exist['end_time'] <= date('Y-m-d H:i:s')) {
                return ajax('活动已结束', 25);
            }
            if ($exist['vote_time'] <= date('Y-m-d H:i:s')) {
                return ajax('报名时间已结束', 30);
            }
            Db::table('mp_design_works')->where($where)->setInc('vote', 1);
            $insert_data = [
                'work_id' => $val['work_id'],
                'uid' => $this->myinfo['uid'],
                'vip' => $user['vip'],
                'req_id' => $workExist['req_id'],
                'create_time' => time()
            ];
            Db::table('mp_vote')->insert($insert_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
//设计师列表
    public function designerList() {
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        try {
            $where = [
                ['role', '=', 3]
            ];
            $whereFocus = [
                ['uid','=',$this->myinfo['uid']]
            ];
            $myFocus = Db::table('mp_focus')->where($whereFocus)->column('to_uid');
            $list = Db::table('mp_user')
                ->where($where)
                ->field("id,nickname,avatar,focus,sex,age")
                ->limit(($curr_page - 1) * $perpage, $perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            if(in_array($v['id'],$myFocus)) {
                $v['if_focus'] = true;
            }else {
                $v['if_focus'] = false;
            }
        }
        return ajax($list);
    }
//设计师参赛作品
    public function designerReqWorkList()
    {
        $val['uid'] = input('post.uid');
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        $this->checkPost($val);
        try {
            $where = [
                ['type', '=', 2],
                ['uid', '=', $val['uid']]
            ];
            $list = Db::table('mp_design_works')
                ->where($where)
                ->field("id,title,req_id,vote,pics")
                ->limit(($curr_page - 1) * $perpage, $perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['cover'] = unserialize($v['pics'])[0];
            unset($v['pics']);
        }
        return ajax($list);
    }
//设计师展示作品
    public function designerShowWorkList()
    {
        $val['uid'] = input('post.uid');
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        $this->checkPost($val);
        try {
            $where = [
                ['type', '=', 1],
                ['uid', '=', $val['uid']]
            ];
            $list = Db::table('mp_design_works')
                ->where($where)
                ->field("id,title,pics")
                ->limit(($curr_page - 1) * $perpage, $perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['cover'] = unserialize($v['pics'])[0];
            unset($v['pics']);
        }
        return ajax($list);
    }
//设计师详情
    public function designerDetail() {
        $val['uid'] = input('post.uid');
        $this->checkPost($val);
        try {
            $whereFocus = [
                ['uid','=',$this->myinfo['uid']]
            ];
            $myFocus = Db::table('mp_focus')->where($whereFocus)->column('to_uid');
            $info = Db::table('mp_user')->alias('u')
                ->join("mp_role r","u.id=r.uid","left")
                ->where('u.id', $val['uid'])
                ->field("u.id,u.nickname,u.avatar,u.sex,u.focus,u.age,r.desc")
                ->find();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        if(in_array($info['id'],$myFocus)) {
            $info['if_focus'] = true;
        }else {
            $info['if_focus'] = false;
        }
        return ajax($info);

    }
//充值类目列表
    public function getVipList()
    {
        try {
            $list = Db::table('mp_vip')->where([
                ['status', '=', 1]
            ])->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }
//充值
    public function recharge()
    {
        $val['vip_id'] = input('post.vip_id');
        $val['name'] = input('post.name');
        $val['tel'] = input('post.tel');
        $val['address'] = input('post.address');
        $val['uid'] = $this->myinfo['uid'];

        $this->checkPost($val);
        try {
            $exist = Db::table('mp_vip')->where('id', $val['vip_id'])->find();
            if (!$exist) {
                return ajax('invalid vip_id', -4);
            }
            $val['price'] = $exist['price'];
            $val['days'] = $exist['days'];
            $val['create_time'] = time();
            $val['order_sn'] = create_unique_number('v');
            Db::table('mp_vip_order')->insert($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($val);

    }
//博文列表
    public function orgList()
    {
        $val['role'] = input('post.role');
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        $this->checkPost($val);
        if (!in_array($val['role'], [1, 2])) {
            return ajax($val['role'], -4);
        }
        try {
            $where = [
                ['role', '=', $val['role']]
            ];
            $list = Db::table('mp_role')
                ->where($where)
                ->field("uid,cover,role,org,name,desc")
                ->limit(($curr_page - 1) * $perpage, $perpage)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }
//博文详情
    public function orgDetail()
    {
        $val['uid'] = input('post.uid');
        $this->checkPost($val);
        $where = [
            ['uid', '=', $val['uid']],
            ['role', 'in', [1, 2]]
        ];
        try {
            $info = Db::table('mp_role')->where($where)->field("uid,cover,role,org,name,desc")->find();
            if (!$info) {
                return ajax($val['uid'], -4);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }
//博文工设笔记
    public function userNoteList()
    {
        $page = input('page', 1);
        $perpage = input('perpage', 10);
        $val['uid'] = input('post.uid');
        $this->checkPost($val);
        $where = [
            ['n.uid', '=', $val['uid']],
            ['n.del', '=', 0]
        ];
        try {
            $ret['count'] = Db::table('mp_note')->alias('n')->where($where)->count();
            $list = Db::table('mp_note')->alias('n')
                ->join('mp_user u', 'n.uid=u.id', 'left')
                ->where($where)
                ->field('n.id,n.title,n.pics,n.width,n.height,n.like,n.status')
                ->order(['n.create_time' => 'DESC'])
                ->limit(($page - 1) * $perpage, $perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
        }
        $ret['list'] = $list;
        return ajax($ret);
    }
//博文需求列表
    public function orgReqList()
    {
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        $val['uid'] = input('post.uid');
        $this->checkPost($val);
        $where = [
            ['r.status', '=', 1],
            ['r.show', '=', 1],
            ['r.del', '=', 0],
            ['r.uid', '=', $val['uid']]
        ];
        try {
            $list = Db::table('mp_req')
                ->alias('r')
                ->join("mp_user u", "r.uid=u.id", "left")
                ->where($where)->order(['r.start_time' => 'ASC'])
                ->field("r.id,r.title,r.cover,r.part_num,r.start_time,r.end_time,u.org as user_org")
                ->limit(($curr_page - 1) * $perpage, $perpage)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['start_time'] = date('Y-m-d', strtotime($v['start_time']));
            $v['end_time'] = date('Y-m-d', strtotime($v['end_time']));
        }
        return ajax($list);
    }
//工厂列表
    public function factoryList()
    {
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        try {
            $where = [
                ['role', '=', 4]
            ];
            $list = Db::table('mp_role')
                ->where($where)
                ->field("uid,cover,role,org,name,desc")
                ->limit(($curr_page - 1) * $perpage, $perpage)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        return ajax($list);
    }
//工厂详情
    public function factoryDetail() {
        $val['uid'] = input('post.uid');
        $this->checkPost($val);
        $where = [
            ['uid', '=', $val['uid']],
            ['role', '=', 4]
        ];
        try {
            $info = Db::table('mp_role')->where($where)->field("uid,cover,role,org,desc")->find();
            if (!$info) {
                return ajax($val['uid'], -4);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }
//工厂竞标列表
    public function factoryBiddingList()
    {
        $val['uid'] = input('post.uid');
        $curr_page = input('post.page', 1);
        $perpage = input('post.perpage', 10);
        $this->checkPost($val);
        try {
            $where = [
                ['b.uid','=',$val['uid']]
            ];
            $list = Db::table('mp_bidding')->alias('b')
                ->join("mp_design_works w","b.work_id=w.id","left")
                ->join("mp_req r","b.req_id=r.id","left")
                ->join("mp_role ro","r.uid=ro.uid","left")
                ->field("b.work_id,b.req_id,b.create_time,w.title as work_title,w.pics,r.title as req_title,ro.org")
                ->limit(($curr_page - 1) * $perpage, $perpage)
                ->where($where)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['pics'] = unserialize($v['pics']);
        }
        return ajax($list);
    }


//上传图片限制512KB
    public function uploadImage()
    {
        if (!empty($_FILES)) {
            if (count($_FILES) > 1) {
                return ajax('最多上传一张图片', 9);
            }
            $path = $this->upload(array_keys($_FILES)[0]);
            return ajax(['path' => $path]);
        } else {
            return ajax('请上传图片', 3);
        }
    }

//上传图片限制2048KB
    public function uploadImage2m()
    {
        if (!empty($_FILES)) {
            if (count($_FILES) > 1) {
                return ajax('最多上传一张图片', 9);
            }
            $path = $this->upload(array_keys($_FILES)[0], 2048);
            return ajax(['path' => $path]);
        } else {
            return ajax('请上传图片', 3);
        }
    }

}