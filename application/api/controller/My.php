<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/11
 * Time: 16:00
 */
namespace app\api\controller;
use my\Sendsms;
use think\Db;
class My extends Common {

    //获取个人信息
    public function mydetail() {
        $map = [
            ['id','=',$this->myinfo['uid']]
        ];
        try {
            $info = Db::table('mp_user')
                ->where($map)
                ->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($info);
    }
    //点击头像编辑个人资料
    public function modMyInfo() {
        $val['realname'] = input('post.realname');
        $val['nickname'] = input('post.nickname');
        $val['sex'] = input('post.sex');
        $val['age'] = input('post.age');
        $val['tel'] = input('post.tel');
        $this->checkPost($val);

        $val['desc'] = input('post.desc','');
        $val['id'] = $this->myinfo['uid'];
        $user = $this->getMyInfo();
        try {
            $avatar = input('post.avatar');
            if($avatar) {
                if (substr($avatar,0,4) == 'http') {
                    $val['avatar'] = $avatar;
                }else {
                    $val['avatar'] = $this->rename_file($avatar,'static/uploads/role/');
                }
            }else {
                return ajax('',3);
            }
            Db::table('mp_user')->where('id',$val['id'])->update($val);
        } catch (\Exception $e) {
            if ($val['avatar'] != $user['avatar']) {
                @unlink($val['avatar']);
            }
            return ajax($e->getMessage(), -1);
        }
        if ($val['avatar'] != $user['avatar']) {
            @unlink($user['avatar']);
        }
        return ajax();

    }
    //获取我发的笔记列表
    public function getMyNoteList()
    {
        $page = input('page',1);
        $perpage = input('perpage',10);
        $where = [
            ['n.uid','=',$this->myinfo['uid']],
            ['n.del','=',0]
        ];
        try {
            $ret['count'] = Db::table('mp_note')->alias('n')->where($where)->count();
            $list = Db::table('mp_note')->alias('n')
                ->join('mp_user u','n.uid=u.id','left')
                ->where($where)
                ->field('n.id,n.title,n.pics,u.nickname,n.like,n.status')
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
    //获取我自己的笔记详情
    public function getMyNoteDetail() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $val['uid'] = $this->myinfo['uid'];
        try {
            $where = [
                ['id','=',$val['id']],
                ['uid','=',$val['uid']],
                ['del','=',0]
            ];
            $exist = Db::table('mp_note')->where($where)
                ->field("id,title,content,pics,status,reason")
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
    //编辑笔记
    public function noteMod ()
    {
        $val['id'] = input('post.id');
        $val['title'] = input('post.title');
        $val['content'] = input('post.content');
        $this->checkPost($val);
        $val['uid'] = $this->myinfo['uid'];
        $image = input('post.pics',[]);

        $where = [
            ['id','=',$val['id']],
            ['uid','=',$this->myinfo['uid']]
        ];
        try {
            $exist = Db::table('mp_note')->where($where)->find();
            if(!$exist) {
                return ajax($val['id'],-4);
            }
            if($exist['status'] == 0) {
                return ajax('当前状态无法修改',34);
            }
            $old_pics = unserialize($exist['pics']);
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
            $image_array = [];
            foreach ($image as $v) {
                $image_array[] = $this->rename_file($v);
            }
            $val['pics'] = serialize($image_array);
            $val['status'] = 0;
            Db::table('mp_note')->where($where)->update($val);
        }catch (\Exception $e) {
            foreach ($image_array as $v) {
                if(!in_array($v,$old_pics)) {
                    @unlink($v);
                }
            }
            return ajax($e->getMessage(),-1);
        }
        foreach ($old_pics as $v) {
            if(!in_array($v,$image_array)) {
                @unlink($v);
            }
        }
        return ajax();
    }
    //获取我的收藏笔记列表
    public function getMyCollectedNoteList() {
        $page = input('page',1);
        $perpage = input('perpage',10);
        $where = [
            ['c.uid','=',$this->myinfo['uid']]
        ];
        try {
            $ret['count'] = Db::table('mp_collect')->alias('c')
                ->join('mp_note n','c.note_id=n.id','left')
                ->join('mp_user u','c.uid=u.id','left')
                ->where($where)->count();
            $list = Db::table('mp_collect')->alias('c')
                ->join('mp_note n','c.note_id=n.id','left')
                ->join('mp_user u','n.uid=u.id','left')
                ->where($where)
                ->field('n.id,n.title,n.pics,u.nickname,u.avatar,n.like')
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
    //发布需求
    public function reqRelease() {
        $val['title'] = input('post.title');
        $val['org'] = input('post.org');
        $val['explain'] = input('post.explain');
        $val['theme'] = input('post.theme');
        $val['part_obj'] = input('post.part_obj');
        $val['part_way'] = input('post.part_way');
        $val['linkman'] = input('post.linkman');
        $val['tel'] = input('post.tel');
        $val['start_time'] = input('post.start_time');
        $val['deadline'] = input('post.deadline');
        $val['vote_time'] = input('post.vote_time');
        $val['end_time'] = input('post.end_time');
        $val['uid'] = $this->myinfo['uid'];
        $this->checkPost($val);
        $val['weixin'] = input('post.weixin');

        $user = Db::table('mp_user')->where('id',$val['uid'])->find();
        if(!in_array($user['role'],[1,2]) || $user['auth'] != 2) {
            return ajax('当前角色状态无法发布需求',24);
        }

        $image = input('post.cover','');
        if($image) {
            if(!file_exists($image)) {
                return ajax($image,5);
            }
            $val['cover'] = $this->rename_file($image,'static/uploads/req/');
        }else {
            return ajax('请传入图片',3);
        }
        try {
            $val['deadline'] = date('Y-m-d 23:59:59',strtotime($val['deadline']));
            $val['vote_time'] = date('Y-m-d 23:59:59',strtotime($val['vote_time']));
            $val['end_time'] = date('Y-m-d 23:59:59',strtotime($val['end_time']));
            Db::table('mp_req')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['cover'])) {
                @unlink($val['cover']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax();

    }
    //我发布的需求或我参与的需求列表
    public function myReqList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $val['uid'] = $this->myinfo['uid'];
        $where = [];
        try {
            $user = $this->getMyInfo();
            if($user['auth'] != 2) {
                return ajax([]);
            }
            if(in_array($user['role'],[1,2])) {
                $where = [
                    ['r.show','=',1],
                    ['r.del','=',0],
                    ['r.uid','=',$val['uid']]
                ];
            }
            if($user['role'] == 3) {
                $req_ids = Db::table('mp_design_works')->where([
                    ['uid','=',$val['uid']],
                    ['type','=',2]
                ])->column('req_id');
                if(!$req_ids) {
                    return ajax([]);
                }
                $where = [
                    ['r.status','=',1],
                    ['r.show','=',1],
                    ['r.del','=',0],
                    ['r.id','in',$req_ids]
                ];
            }
            if($user['role'] == 4) {
                return ajax([]);
            }
            $list = Db::table('mp_req')
                ->alias('r')
                ->join("mp_user u","r.uid=u.id","left")
                ->where($where)->order(['r.start_time'=>'ASC'])
                ->field("r.id,r.title,r.cover,r.part_num,r.status,r.start_time,r.end_time,u.org as user_org")
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
    //获取需求详情
    public function reqDetail() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        try {
            $where = [
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
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($info);
    }
    //编辑需求
    public function reqMod() {
        $val['title'] = input('post.title');
        $val['org'] = input('post.org');
        $val['explain'] = input('post.explain');
        $val['theme'] = input('post.theme');
        $val['part_obj'] = input('post.part_obj');
        $val['part_way'] = input('post.part_way');
        $val['linkman'] = input('post.linkman');
        $val['tel'] = input('post.tel');
        $val['start_time'] = input('post.start_time');
        $val['deadline'] = input('post.deadline');
        $val['vote_time'] = input('post.vote_time');
        $val['end_time'] = input('post.end_time');
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $val['uid'] = $this->myinfo['uid'];
        $val['weixin'] = input('post.weixin');

        $user = $this->getMyInfo();
        if(!in_array($user['role'],[1,2]) || $user['auth'] != 2) {
            return ajax('当前角色状态无法发布需求',24);
        }
        try {
            $where = [
                ['id','=',$val['id']],
                ['uid','=',$val['uid']]
            ];
            $exist = Db::table('mp_req')->where($where)->find();
            if(!$exist) {
                return ajax($val['id'],-4);
            }
            if($exist['status'] != 2) {
                return ajax('当前活动状态无法修改',34);
            }
            $image = input('post.cover','');
            if($image) {
                if(!file_exists($image)) {
                    return ajax($image,5);
                }
                $val['cover'] = $this->rename_file($image,'static/uploads/req/');
            }else {
                return ajax('请传入图片',3);
            }
            $val['deadline'] = date('Y-m-d 23:59:59',strtotime($val['deadline']));
            $val['vote_time'] = date('Y-m-d 23:59:59',strtotime($val['vote_time']));
            $val['end_time'] = date('Y-m-d 23:59:59',strtotime($val['end_time']));
            $val['status'] = 0;
            Db::table('mp_req')->where($where)->update($val);
        }catch (\Exception $e) {
            if(isset($val['cover']) && $val['cover'] != $exist['cover']) {
                @unlink($val['cover']);
            }
            return ajax($e->getMessage(),-1);
        }
        if(isset($val['cover']) && $val['cover'] != $exist['cover']) {
            @unlink($exist['cover']);
        }
        return ajax();
    }
    //上传展示作品
    public function uploadShowWorks() {
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        $this->checkPost($val);
        $val['uid'] = $this->myinfo['uid'];
        $val['type'] = 1;
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
            $image_array = [];
            foreach ($image as $v) {
                $image_array[] = $this->rename_file($v,'static/uploads/work/');
            }
            $val['pics'] = serialize($image_array);
            Db::table('mp_design_works')->insert($val);
        }catch (\Exception $e) {
            foreach ($image_array as $v) {
                @unlink($v);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
    //获取我的展示作品
    public function getMyShowWorks() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        try {
            $where = [
                ['type','=',1],
                ['uid','=',$this->myinfo['uid']]
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
    //获取我的参赛作品
    public function getMyReqWorks() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        try {
            $where = [
                ['type','=',2],
                ['uid','=',$this->myinfo['uid']]
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
    //获取申请审核状态
    public function applyStatus() {
        $uid = $this->myinfo['uid'];
        try {
            $auth = Db::table('mp_user')->where('id',$uid)->field('auth')->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($auth);
    }
    //获取申请信息
    public function applyInfo() {
        $uid = $this->myinfo['uid'];
        try {
            $info = Db::table('mp_role')->where('uid',$uid)->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($info) {
            $info['works'] = unserialize($info['works']);
            return ajax($info);
        }else {
            return ajax([]);
        }
    }
    //申请角色
    public function apply() {
        $val['role'] = input('post.role');
        $val['name'] = input('post.name');
        $val['identity'] = input('post.identity');
        $val['tel'] = input('post.tel');
        $val['code'] = input('post.code');
        $val['uid'] = $this->myinfo['uid'];
        $this->checkPost($val);
        $val['desc'] = input('post.desc');
        $val['org'] = input('post.org','');
        $val['weixin'] = input('post.weixin');
        $id_front = input('post.id_front');
        $id_back = input('post.id_back');
        $cover = input('post.cover');
        $works = input('post.works', []);

        if(!in_array($val['role'],[1,2,3,4])) {
            return ajax($val['role'],-4);
        }
        if (!isCreditNo_simple($val['identity'])) {
            return ajax('', 13);
        }
        if (!is_tel($val['tel'])) {
            return ajax('', 6);
        }
        if(!$cover) {
            return ajax('请上传封面',33);
        }
        if (!file_exists($cover)) {
            return ajax('封面图片不存在', 5);
        }
        if(!$id_front || !$id_back) {
            return ajax('上传身份证正反面',18);
        }
        if (!file_exists($id_front) || !file_exists($id_back)) {
            return ajax('身份证图片不存在', 5);
        }
        try {//验证短信验证码
            $code_exist = Db::table('mp_verify')->where([
                ['tel','=',$val['tel']],
                ['code','=',$val['code']]
            ])->find();
            if($code_exist) {
                if((time() - $code_exist['create_time']) > 60*5) {
                    return ajax('验证码已过期',17);
                }
            }else {
                return ajax('验证码无效',16);
            }
            $image_array = [];//验证设计师作品
            if($val['role'] == 3) {
                if (is_array($works) && !empty($works)) {
                    if (count($works) > 6) {
                        return ajax('最多上传6张作品', 15);
                    }
                    foreach ($works as $v) {
                        if (!file_exists($v)) {
                            return ajax($v, 5);
                        }
                    }
                } else {
                    return ajax('请传入作品', 14);
                }
                foreach ($works as $v) {
                    $image_array[] = $this->rename_file($v);
                }
            }else {
                $license = input('post.license');
                if(!$val['org']) {
                    return ajax('机构名称不能为空',23);
                }
                if(!$license) {
                    return ajax('请传入资质证书',19);
                }
                $val['license'] = $this->rename_file($license,'static/uploads/role/');
            }
            $val['works'] = serialize($image_array);
            $val['cover'] = $this->rename_file($cover,'static/uploads/role/');
            $val['id_front'] = $this->rename_file($id_front,'static/uploads/role/');
            $val['id_back'] = $this->rename_file($id_back,'static/uploads/role/');

            $role_exist = Db::table('mp_role')->where('uid',$val['uid'])->find();
            unset($val['code']);
            if($role_exist) {
                $old_works = unserialize($role_exist['works']);
                Db::table('mp_role')->where('uid',$val['uid'])->update($val);
            }else {
                Db::table('mp_role')->insert($val);
            }
            Db::table('mp_user')->where('id',$val['uid'])->update([
                'role' => $val['role'],
                'auth' => 1,
                'org' => $val['org'],
                'desc' => $val['desc']
            ]);
        }catch (\Exception $e) {//异常删图
            if($role_exist) {
                if(isset($val['license']) && $val['license'] != $role_exist['license']) {
                    @unlink($role_exist['license']);
                }
                foreach ($image_array as $v) {
                    if(!in_array($v,$old_works)) {
                        @unlink($v);
                    }
                }
            }else {
                if(isset($val['license'])) {
                    @unlink($val['license']);
                }
                foreach ($image_array as $v) {
                    @unlink($v);
                }
                @unlink($val['cover']);
                @unlink($val['id_front']);
                @unlink($val['id_back']);
            }
            return ajax($e->getMessage(),-1);
        }
        if($role_exist) {//正常删图
            if(isset($val['license']) && $val['license'] != $role_exist['license']) {
                @unlink($role_exist['license']);
            }
            if($val['cover'] != $role_exist['cover']) {
                @unlink($role_exist['cover']);
            }
            if($val['id_front'] != $role_exist['id_front']) {
                @unlink($role_exist['id_front']);
            }
            if($val['id_back'] != $role_exist['id_back']) {
                @unlink($role_exist['id_back']);
            }
            if($val['role'] == 3) {
                foreach ($old_works as $v) {
                    if(!in_array($v,$image_array)) {
                        @unlink($v);
                    }
                }
            }
        }
        return ajax();
    }
    //我的竞标列表
    public function myBiddingList() {
        $val['uid'] = $this->myinfo['uid'];
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
    //申请角色发送手机短信
    public function sendSms() {
        $val['tel'] = input('post.tel');
        $this->checkPost($val);
        $sms = new Sendsms();
        $tel = $val['tel'];

        if(!is_tel($tel)) {
            return ajax('invalid tel',6);
        }
        try {
            $param = [
                'tel' => $tel,
                'code' => mt_rand(100000,999999),
                'create_time' => time()
            ];
            $exist = Db::table('mp_verify')->where('tel',$tel)->find();
            if($exist) {
                if((time() - $exist['create_time']) < 60) {
                    return ajax('1分钟内不可重复发送',11);
                }
                $res = $sms->send($param);
                if($res->Code === 'OK') {
                    Db::table('mp_verify')->where('tel',$tel)->update($param);
                    return ajax();
                }else {
                    return ajax($res->Message,12);
                }
            }else {
                $res = $sms->send($param);
                if($res->Code === 'OK') {
                    Db::table('mp_verify')->insert($param);
                    return ajax();
                }else {
                    return ajax($res->Message,12);
                }
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }
    //我的订单列表
    public function orderList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $status = input('post.status','');
        $where = [
            ['o.uid','=',$this->myinfo['uid']]
        ];
        if($status !== '') {
            $where[] = ['o.status','=',$status];
            $where[] = ['o.status','in',[0,1,2,3]];
        }
        try {
            $list = Db::table('mp_order')->alias('o')
                ->join("mp_order_detail d","o.id=d.order_id","left")
                ->join("mp_goods g","d.goods_id=g.id","left")
                ->where($where)
                ->field("o.id,o.pay_order_sn,o.pay_price,o.total_price,o.carriage,o.create_time,o.refund_apply,o.status,d.order_id,d.num,d.unit_price,d.goods_name,d.attr,g.pics")
                ->order(['o.id'=>'DESC'])
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $order_id = [];
        $newlist = [];
        foreach ($list as $v) {
            $order_id[] = $v['id'];
        }
        $uniq_order_id = array_unique($order_id);
        foreach ($uniq_order_id as $v) {
            $child = [];
            foreach ($list as $li) {
                if($li['order_id'] == $v) {
                    $data['pay_order_sn'] = $li['pay_order_sn'];
                    $data['total_price'] = $li['total_price'];
                    $data['carriage'] = $li['carriage'];
                    $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
                    $data_child['cover'] = unserialize($li['pics'])[0];
                    $data_child['goods_name'] = $li['goods_name'];
                    $data_child['num'] = $li['num'];
                    $data_child['unit_price'] = $li['unit_price'];
                    $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                    $data_child['attr'] = $li['attr'];
                    $child[] = $data_child;
                }
            }
            $data['child'] = $child;
            $newlist[] = $data;
        }
        return ajax($newlist);
    }

    public function refundList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $type = input('post.status',1);
        if(!in_array($type,[1,2])) {
            return ajax($type,-4);
        }
        if($type == 1) {
            $where = [
                ['o.uid','=',$this->myinfo['uid']],
                ['o.refund_apply','=',1]
            ];
        }else {
            $where = [
                ['o.uid','=',$this->myinfo['uid']],
                ['o.refund_apply','=',1],
                ['o.status','=',4]
            ];
        }
        try {
            $list = Db::table('mp_order')->alias('o')
                ->join("mp_order_detail d","o.id=d.order_id","left")
                ->join("mp_goods g","d.goods_id=g.id","left")
                ->where($where)
                ->field("o.id,o.pay_order_sn,o.pay_price,o.total_price,o.carriage,o.create_time,o.refund_apply,o.status,d.order_id,d.num,d.unit_price,d.goods_name,d.attr,g.pics")
                ->order(['o.id'=>'DESC'])
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $order_id = [];
        $newlist = [];
        foreach ($list as $v) {
            $order_id[] = $v['id'];
        }
        $uniq_order_id = array_unique($order_id);
        foreach ($uniq_order_id as $v) {
            $child = [];
            foreach ($list as $li) {
                if($li['order_id'] == $v) {
                    $data['pay_order_sn'] = $li['pay_order_sn'];
                    $data['total_price'] = $li['total_price'];
                    $data['carriage'] = $li['carriage'];
                    $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
                    $data_child['cover'] = unserialize($li['pics'])[0];
                    $data_child['goods_name'] = $li['goods_name'];
                    $data_child['num'] = $li['num'];
                    $data_child['unit_price'] = $li['unit_price'];
                    $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                    $data_child['attr'] = $li['attr'];
                    $child[] = $data_child;
                }
            }
            $data['child'] = $child;
            $newlist[] = $data;
        }
        return ajax($newlist);
    }
    //查看订单详情
    public function orderDetail() {
        $val['id'] = input('post.id');
        $this->checkPost($val);
        $where = [
            ['o.id','=',$val['id']],
            ['o.uid','=',$this->myinfo['uid']]
        ];
        try {
            $list = Db::table('mp_order')->alias('o')
                ->join("mp_order_detail d","o.id=d.order_id","left")
                ->join("mp_goods g","d.goods_id=g.id","left")
                ->where($where)
                ->field("o.id,o.pay_order_sn,o.pay_price,o.total_price,o.carriage,o.receiver,o.tel,o.address,o.create_time,o.refund_apply,o.status,d.order_id,d.num,d.unit_price,d.goods_name,d.attr,g.pics")->select();
            if(!$list) {
                return ajax($val['id'],-4);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $data = [];
        $child = [];
        foreach ($list as $li) {
            $data['pay_order_sn'] = $li['pay_order_sn'];
            $data['receiver'] = $li['receiver'];
            $data['tel'] = $li['tel'];
            $data['address'] = $li['address'];
            $data['total_price'] = $li['total_price'];
            $data['carriage'] = $li['carriage'];
            $data['amount'] = $li['total_price'] - $data['carriage'];
            $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
            $data['refund_apply'] = $li['refund_apply'];
            $data['status'] = $li['status'];
            $data_child['cover'] = unserialize($li['pics'])[0];
            $data_child['goods_name'] = $li['goods_name'];
            $data_child['num'] = $li['num'];
            $data_child['unit_price'] = $li['unit_price'];
            $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
            $data_child['attr'] = $li['attr'];
            $data_child['cover'] = unserialize($li['pics'])[0];
            $child[] = $data_child;
        }
        $data['child'] = $child;
        return ajax($data);

    }
    //申请退款
    public function refundApply() {
        $val['order_sn'] = input('post.order_sn');
        $val['reason'] = input('post.reason');
        $this->checkPost($val);
        try {
            $where = [
                ['order_sn','=',$val['order_sn']],
                ['uid','=',$this->myinfo['uid']],
                ['status','in',[1,2,3]]
            ];
            $exist = Db::table('mp_order')->alias('o')->where($where)->find();
            if(!$exist) {
                return ajax( 'invalid order_sn',-4);
            }
            $update_data = [
                'refund_apply' => 1,
                'reason' => $val['reason']
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //确认收货
    public function orderConfirm() {
        $val['order_sn'] = input('post.order_sn');
        $this->checkPost($val);
        try {
            $where = [
                ['order_sn','=',$val['order_sn']],
                ['uid','=',$this->myinfo['uid']],
                ['status','=',2]
            ];
            $exist = Db::table('mp_order')->alias('o')->where($where)->find();
            if(!$exist) {
                return ajax( 'invalid order_sn',-4);
            }
            $update_data = [
                'status' => 3
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //删除订单
    public function orderDel() {
        $val['pay_order_sn'] = input('post.pay_order_sn');
        $this->checkPost($val);
        try {
            $where = [
                ['pay_order_sn','=',$val['pay_order_sn']],
                ['uid','=',$this->myinfo['uid']],
                ['status','=',0]
            ];
            $exist = Db::table('mp_order')->alias('o')->where($where)->find();
            if(!$exist) {
                return ajax( 'invalid order_sn',-4);
            }
            $update_data = [
                'del' => 1
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    //我的裂变二维码
    public function myQrcode() {

    }







}