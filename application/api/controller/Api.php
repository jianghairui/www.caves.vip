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

class Api extends Common {
//获取轮播图列表
    public function slideList() {
        try {
            $list = Db::table('mp_slideshow')->where([
                ['status','=',1]
            ])->order(['sort'=>'ASC'])->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }
//获取活动列表
    public function getReqList() {
        $type = input('post.type','');
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        if(!in_array($type,[1,2])) {
            return ajax($type,-4);
        }
        $where = [];
        if($type == 1) {
            $where = [
                ['r.vote_time','>',date('Y-m-d H:i:s')],
                ['r.status','=',1],
                ['r.show','=',1],
                ['r.del','=',0]
            ];
        }
        if($type == 2) {
            $where = [
                ['r.vote_time','<=',date('Y-m-d H:i:s')],
                ['r.end_time','>',date('Y-m-d H:i:s')],
                ['r.status','=',1],
                ['r.show','=',1],
                ['r.del','=',0]
            ];
        }

        try {
            $list = Db::table('mp_req')
                ->alias('r')
                ->join("mp_user u","r.uid=u.id","left")
                ->where($where)->order(['r.start_time'=>'ASC'])
                ->field("r.id,r.title,r.cover,r.start_time,r.end_time,u.org as user_org")
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['start_time'] = date('Y-m-d',strtotime($v['start_time']));
            $v['end_time'] = date('Y-m-d',strtotime($v['end_time']));
        }
        return ajax($list);
    }
//获取活动详情
    public function getReqDetail() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $where = [
                ['r.status','=',1],
                ['r.show','=',1],
                ['r.del','=',0],
                ['r.id','=',$val['id']],
            ];
            $info = Db::table('mp_req')->alias('r')
                ->join("mp_user u","r.uid=u.id","left")
                ->where($where)
                ->field("r.*,u.org as user_org")
                ->find();
            if(!$info) {
                return ajax($val['id'],-4);
            }
            if(date('Y-m-d 23:59:59',strtotime($info['end_time'])) <= date('Y-m-d H:i:s')) {
                return ajax('活动已结束,无法查看',25);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($info);
    }
//我要参加
    public function takePartIn() {
        $val['req_id'] = input('post.req_id');
        $this->checkPost($val);
        $user = $this->getMyInfo();
        try {
            $where = [
                ['id','=',$val['req_id']]
            ];
            $exist = Db::table('mp_req')->where($where)->find();
            if(!$exist) {
                return ajax('非法参数',-4);
            }
            if($exist['start_time'] > date('Y-m-d H:i:s')) {
                return ajax('活动未开始',26);
            }
            if($exist['end_time'] <= date('Y-m-d H:i:s')) {
                return ajax('活动已结束',25);
            }
            if($exist['deadline'] <= date('Y-m-d H:i:s')) {
                return ajax('报名时间已结束',27);
            }
            if($user['role'] != 3) {
                return ajax('只有设计师可以参加',28);
            }
            if($user['auth'] != 2) {
                return ajax('角色未认证',29);
            }
            $where = [
                ['req_id','=',$val['req_id']],
                ['type','=',2],
                ['uid','=',$this->myinfo['uid']]
            ];
            $workExist = Db::table('mp_design_works')->where($where)->find();
            if($workExist) {
                return ajax('已参加',31);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//上传参赛作品
    public function uploadWorks() {
        $val['req_id'] = input('post.req_id');
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        $this->checkPost($val);
        $user = $this->getMyInfo();
        $val['uid'] = $this->myinfo['uid'];
        $val['type'] = 2;
        $val['create_time'] = time();
        $image = input('post.pics',[]);
        if(is_array($image) && !empty($image)) {
            if(count($image) > 9) {
                return ajax('最多上传9张图片',8);
            }
            foreach ($image as $v) {
                if(!file_exists($v)) {
                    return ajax($v,5);
                }
            }
        }else {
            return ajax('请传入图片',3);
        }
        try {
            $where = [
                ['id','=',$val['req_id']]
            ];
            $exist = Db::table('mp_req')->where($where)->find();
            if(!$exist) {
                return ajax('非法参数',-4);
            }
            if($exist['start_time'] > date('Y-m-d H:i:s')) {
                return ajax('活动未开始',26);
            }
            if($exist['end_time'] <= date('Y-m-d H:i:s')) {
                return ajax('活动已结束',25);
            }
            if($exist['deadline'] <= date('Y-m-d H:i:s')) {
                return ajax('投稿时间已结束',27);
            }
            if($user['role'] != 3) {
                return ajax('只有设计师可以参加',28);
            }
            if($user['auth'] != 2) {
                return ajax('角色还未认证',29);
            }
            $image_array = [];
            foreach ($image as $v) {
                $image_array[] = $this->rename_file($v,'static/uploads/work/');
            }
            $val['pics'] = serialize($image_array);
            $where = [
                ['req_id','=',$val['req_id']],
                ['type','=',2],
                ['uid','=',$this->myinfo['uid']]
            ];
            $workExist = Db::table('mp_design_works')->where($where)->find();
            if($workExist) {
                foreach ($image_array as $v) {
                    @unlink($v);
                }
                return ajax('已参加',31);
            }
            Db::table('mp_design_works')->insert($val);
        }catch (\Exception $e) {
            foreach ($image_array as $v) {
                @unlink($v);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//获取参赛作品列表
    public function worksList() {
        $val['req_id'] = input('post.req_id');
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $this->checkPost($val);
        try {
            $where = [
                ['w.req_id','=',$val['req_id']]
            ];
            $list = Db::table('mp_design_works')->alias('w')
                ->join("mp_req r","w.req_id=r.id","left")
                ->join("mp_user u","w.uid=u.id","left")
                ->where($where)
                ->field("w.id,w.title,w.vote,w.pics,u.nickname,u.avatar")
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['cover'] = unserialize($v['pics'])[0];
            unset($v['pics']);
        }
        return ajax($list);
    }
//参赛作品详情
    public function worksDetail() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $exist = Db::table('mp_design_works')->alias('w')
                ->join("mp_user u","w.uid=u.id","left")
                ->join("mp_role r","w.uid=r.uid","left")
                ->where('w.id',$val['id'])
                ->field("w.id,w.title,w.desc,w.pics,u.avatar,r.name")
                ->find();
            if(!$exist) {
                return ajax($val['id'],-4);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $exist['pics'] = unserialize($exist['pics']);
        return ajax($exist);
    }
//投票
    public function vote() {
        $val['work_id'] = input('post.work_id');
        $this->checkPost($val);
        $user = $this->getMyInfo();
        try {
            $where = [
                ['id','=',$val['work_id']],
                ['type','=',2]
            ];
            $workExist = Db::table('mp_design_works')->where($where)->find();
            if(!$workExist) {
                return ajax($val['work_id'],-4);
            }
            $map = [
                ['id','=',$workExist['req_id']]
            ];
            $exist = Db::table('mp_req')->where($map)->find();
            if($exist['end_time'] <= date('Y-m-d H:i:s')) {
                return ajax('活动已结束',25);
            }
            if($exist['vote_time'] <= date('Y-m-d H:i:s')) {
                return ajax('报名时间已结束',30);
            }
            Db::table('mp_design_works')->where($where)->setInc('vote',1);
            $insert_data = [
                'work_id'=>$val['work_id'],
                'uid'=>$this->myinfo['uid'],
                'vip'=>$user['vip'],
                'req_id'=>$workExist['req_id'],
                'create_time'=>time()
            ];
            Db::table('mp_vote')->insert($insert_data);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
//设计师列表
    public function designerList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        try {
            $where = [
                ['role','=',3]
            ];
            $list = Db::table('mp_user')
                ->where($where)
                ->field("id,nickname,avatar,focus,sex,age")
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }
//设计师参赛作品
    public function designerReqWorkList() {
        $val['uid'] = input('post.uid');
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $this->checkPost($val);
        try {
            $where = [
                ['type','=',2],
                ['uid','=',$val['uid']]
            ];
            $list = Db::table('mp_design_works')
                ->where($where)
                ->field("id,title,req_id,vote,pics")
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['cover'] = unserialize($v['pics'])[0];
            unset($v['pics']);
        }
        return ajax($list);
    }
//设计师
    public function designerShowWorkList() {
        $val['uid'] = input('post.uid');
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $this->checkPost($val);
        try {
            $where = [
                ['type','=',1],
                ['uid','=',$val['uid']]
            ];
            $list = Db::table('mp_design_works')
                ->where($where)
                ->field("id,title,pics")
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        foreach ($list as &$v) {
            $v['cover'] = unserialize($v['pics'])[0];
            unset($v['pics']);
        }
        return ajax($list);
    }




    public function getVipList() {
        try {
            $list = Db::table('mp_vip')->where([
                ['status','=',1]
            ])->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }

    public function recharge() {
        $val['vip_id'] = input('post.vip_id');
        $val['name'] = input('post.name');
        $val['tel'] = input('post.tel');
        $val['address'] = input('post.address');
        $val['uid'] = $this->myinfo['uid'];

        $this->checkPost($val);
        try {
            $exist = Db::table('mp_vip')->where('id',$val['vip_id'])->find();
            if(!$exist) {
                return ajax('invalid vip_id',-4);
            }
            $val['price'] = $exist['price'];
            $val['days'] = $exist['days'];
            $val['create_time'] = time();
            $val['order_sn'] = create_unique_number('v');
            Db::table('mp_vip_order')->insert($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val);

    }

//上传图片限制512KB
    public function uploadImage() {
        if(!empty($_FILES)) {
            if(count($_FILES) > 1) {
                return ajax('最多上传一张图片',9);
            }
            $path = $this->upload(array_keys($_FILES)[0]);
            return ajax(['path'=>$path]);
        }else {
            return ajax('请上传图片',3);
        }
    }
//上传图片限制2048KB
    public function uploadImage2m() {
        if(!empty($_FILES)) {
            if(count($_FILES) > 1) {
                return ajax('最多上传一张图片',9);
            }
            $path = $this->upload(array_keys($_FILES)[0],2048);
            return ajax(['path'=>$path]);
        }else {
            return ajax('请上传图片',3);
        }
    }

}