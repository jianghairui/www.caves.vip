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

    public function takePartIn() {
        $val['req_id'] = input('post.req_id');
        $this->checkPost($val);
        try {
            $where = [
                ['id','=',$val['req_id']]
            ];
            $exist = Db::table('mp_req')->where($where)->find();
            if(!$exist) {
                return ajax('非法参数',-4);
            }
            if($exist['start_time'] < date('Y-m-d H:i:s')) {
                return ajax();
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $user = $this->getMyInfo();
        return ajax($user);
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