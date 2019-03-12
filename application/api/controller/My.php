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
            $info = Db::table('mp_user')->where($map)->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($info);
    }
    //获取我发的笔记列表
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
    //获取我的收藏笔记列表
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
            return ajax($info);
        }else {
            return ajax([]);
        }
    }
    //申请角色
    public function apply()
    {
        $val['role'] = input('post.role');
        $val['name'] = input('post.name');
        $val['identity'] = input('post.identity');
        $val['tel'] = input('post.tel');
        $val['code'] = input('post.code');
        $val['uid'] = $this->myinfo['uid'];
        $this->checkPost($val);

        if(!in_array($val['role'],[1,2,3,4])) {
            return ajax($val['role'],-4);
        }
        if (!isCreditNo_simple($val['identity'])) {
            return ajax('', 13);
        }
        if (!is_tel($val['tel'])) {
            return ajax('', 6);
        }
        try {
            $code_exist = Db::table('mp_verify')->where([
                ['tel','=',$val['tel']],
                ['code','=',$val['code']]
            ])->find();
            if($code_exist) {
                if((time() - $code_exist['create_time']) > 60*5) {
                    return ajax('',17);
                }
            }else {
                return ajax('',16);
            }
            $id_front = input('post.id_front');
            $id_back = input('post.id_back');
            if(!$id_front || !$id_back) {
                return ajax('',18);
            }
            if (!file_exists($id_front) || !file_exists($id_back)) {
                return ajax('invalid id_image', 5);
            }

            $val['id_front'] = $this->rename_file($id_front,'static/uploads/role/');
            $val['id_back'] = $this->rename_file($id_back,'static/uploads/role/');
            $val['weixin'] = input('post.weixin');
            $images = input('post.pics', []);
            $val['org'] = input('post.org');

            $image_array = [];
            if($val['role'] == 3) {
                if (is_array($images) && !empty($images)) {
                    if (count($images) > 6) {
                        return ajax('最多上传6张作品', 15);
                    }
                    foreach ($images as $v) {
                        if (!file_exists($v)) {
                            return ajax($v, 5);
                        }
                    }
                } else {
                    return ajax('请传入作品', 14);
                }
                foreach ($images as $v) {
                    $image_array[] = $this->rename_file($v);
                }
            }else {
                $license = input('post.license');
                if(!$license) {
                    return ajax('请传入资质证书',19);
                }
                $val['license'] = $this->rename_file($license,'static/uploads/role/');
            }

            $val['works'] = serialize($image_array);
            unset($val['code']);
            Db::table('mp_role')->insert($val);
        }catch (\Exception $e) {
            foreach ($image_array as $v) {
                @unlink($v);
            }
            if(isset($val['license'])) {
                @unlink($val['license']);
            }
            @unlink($val['id_front']);
            @unlink($val['id_back']);
            return ajax($e->getMessage(),-1);
        }
        return ajax();

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







}