<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/11
 * Time: 14:27
 */
namespace app\api\controller;

use think\Db;
class Note extends Common {

    //获取笔记列表
    public function getNoteList()
    {
        $page = input('page',1);
        $perpage = input('perpage',10);
        $where = [];
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
    //发布笔记
    public function noteRelease ()
    {
        $val['title'] = input('post.title');
        $val['content'] = input('post.content');
        $this->checkPost($val);
        $val['uid'] = $this->myinfo['uid'];
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
        $image_array = [];
        foreach ($image as $v) {
            $image_array[] = $this->rename_file($v);
        }
        $val['pics'] = serialize($image_array);
        try {
            Db::table('mp_note')->insert($val);
        }catch (\Exception $e) {
            foreach ($image_array as $v) {
                @unlink('.' . $v);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax($val,1);
    }
    //获取笔记详情
    public function getNoteDetail() {
        $id = input('post.id');
        if(!$id) {
            return ajax('id不能为空',-2);
        }
        try {
            $info = Db::table('mp_note')->alias('n')
                ->join('mp_user u','n.uid=u.id','left')
                ->where('n.id',$id)
                ->field('n.*,u.nickname,u.avatar')
                ->find();
            if(!$info) {
                return ajax('invalid id',-4);
            }
            $map = [
                ['to_cid','=',0],
                ['note_id','=',$id]
            ];
            $info['comment_count'] =
                Db::table('mp_comment')->alias('c')
                    ->join('mp_user u','c.uid=u.id','left')
                    ->where($map)->count();
            $info['comment_list'] = Db::table('mp_comment')->alias('c')
                ->join('mp_user u','c.uid=u.id','left')
                ->where($map)
                ->field('c.*,u.nickname,u.avatar')
                ->limit(0,2)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        $info['pics'] = unserialize($info['pics']);
        return ajax($info);
    }
    //获取评论列表
    public function commentList() {
        $val['note_id'] = input('post.note_id');
        $this->checkPost($val);
        try {
            $exist = Db::table('mp_note')->where('id',$val['note_id'])->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            $list = DB::query("SELECT c.id,c.note_id,c.uid,c.to_cid,c.to_uid,c.content,c.root_cid,c.created_time,u.avatar,u.nickname,IFNULL(u2.nickname,'') AS to_nickname 
FROM mp_comment c 
LEFT JOIN mp_user u ON c.uid=u.id 
LEFT JOIN mp_user u2 ON c.to_uid=u2.id 
WHERE c.note_id=?",[$val['note_id']]);
            $list = $this->recursion($list);
            return ajax($list);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }
    //发表评论
    public function commentAdd() {
        $val['note_id'] = input('post.note_id');
        $val['content'] = input('post.content');
        $this->checkPost($val);
        $val['uid'] = $this->myinfo['uid'];
        $val['to_cid'] = input('post.to_cid');

        try {
            $exist = Db::table('mp_note')->where('id',$val['note_id'])->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            if($val['to_cid']) {
                $map = [
                    ['id','=',$val['to_cid']],
                    ['note_id','=',$val['note_id']]
                ];
                $comment_exist = Db::table('mp_comment')->where($map)->find();
                if($comment_exist) {
                    $val['to_uid'] = $comment_exist['uid'];
                    if($comment_exist['to_cid'] == 0) {
                        $val['root_cid'] = $comment_exist['id'];
                    }else {
                        $val['root_cid'] = $comment_exist['root_cid'];
                    }
                }else {
                    return ajax('',-4);
                }
            }else {
                $val['to_cid'] = 0;
                $val['to_uid'] = 0;
                $val['root_cid'] = 0;
            }
            $val['created_time'] = date("Y-m-d H:i:s");
            Db::table('mp_comment')->insert($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
    //判断是否点赞
    public function ifLike() {
        $val['note_id'] = input('post.note_id');
        $this->checkPost($val);
        $val['uid'] = $this->myinfo['uid'];

        try {
            $exist = Db::table('mp_note')->where('id',$val['note_id'])->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            $map = [
                ['uid','=',$val['uid']],
                ['note_id','=',$val['note_id']]
            ];
            $exist = Db::table('mp_like')->where($map)->find();
            if($exist) {
                $like = true;
            }else {
                $like = false;
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($like);
    }
    //点赞,取消
    public function iLike() {
        $val['note_id'] = input('post.note_id');
        $this->checkPost($val);
        $val['uid'] = $this->myinfo['uid'];
        try {
            $exist = Db::table('mp_note')->where('id',$val['note_id'])->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            $map = [
                ['uid','=',$val['uid']],
                ['note_id','=',$val['note_id']]
            ];
            $exist = Db::table('mp_like')->where($map)->find();
            if($exist) {
                Db::table('mp_like')->where($map)->delete();
                Db::table('mp_note')->where('id',$val['note_id'])->setDec('like',1);
                return ajax(false);
            }
            Db::table('mp_like')->insert($val);
            Db::table('mp_note')->where('id',$val['note_id'])->setInc('like',1);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax(true);
    }
    //判断是否收藏
    public function ifCollect() {
        $val['note_id'] = input('post.note_id');
        $this->checkPost($val);
        $val['uid'] = $this->myinfo['uid'];
        try {
            $exist = Db::table('mp_note')->where('id',$val['note_id'])->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            $map = [
                ['uid','=',$val['uid']],
                ['note_id','=',$val['note_id']]
            ];
            $exist = Db::table('mp_collect')->where($map)->find();
            if($exist) {
                $like = true;
            }else {
                $like = false;
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($like);
    }
    //收藏/取消收藏
    public function iCollect() {
        $val['note_id'] = input('post.note_id');
        $this->checkPost($val);
        $val['uid'] = $this->myinfo['uid'];
        try {
            $exist = Db::table('mp_note')->where('id',$val['note_id'])->find();
            if(!$exist) {
                return ajax('invalid note_id',-4);
            }
            $map = [
                ['uid','=',$val['uid']],
                ['note_id','=',$val['note_id']]
            ];
            $exist = Db::table('mp_collect')->where($map)->find();
            if($exist) {
                Db::table('mp_collect')->where($map)->delete();
                Db::table('mp_note')->where('id',$val['note_id'])->setDec('like',1);
                return ajax(false);
            }
            Db::table('mp_collect')->insert($val);
            Db::table('mp_note')->where('id',$val['note_id'])->setInc('like',1);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax(true);
    }




    //获取小程序入驻状态
    public function getStatus() {
        $val['uid'] = input('post.uid');
        $this->checkPost($val);
        try {
            $exist = Db::table('mp_user')->where('id',$val['uid'])->find();
            if(!$exist) {
                return ajax('',-3);
            }
            $order_exist = Db::table('mp_order')->where('uid',$val['uid'])->find();
            if(!$order_exist) {
                $order_exist['flag'] = 0;//第一次进页面,去填写资料
            }else {
                if($order_exist['enter'] == 0 && $order_exist['status'] == 0) {
                    $order_exist['flag'] = 1;//资料已填写,未支付,去支付
                }
                if($order_exist['enter'] == 0 && $order_exist['status'] == 1) {
                    $order_exist['flag'] = 2;//资料已填写,已支付,审核中状态
                }
                if($order_exist['enter'] == -1 && $order_exist['status'] == 1) {
                    $order_exist['flag'] = 3;//资料已填写,已支付,审核未通过,重新填写资料,提交后,刷新页面
                }
                if($order_exist['enter'] == 1) {
                    $order_exist['flag'] = 4;//已入驻
                }
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($order_exist);
    }
    //小程序入驻下单
    public function order() {
        $val['uid'] = input('post.uid');
        $val['linkman'] = input('post.linkman');
        $val['tel'] = input('post.tel');
        $val['email'] = input('post.email');
        $val['set'] = input('post.set');
        $this->checkPost($val);
        $val['cert'] = input('post.cert');


        if(!in_array($val['set'],[1,2,3])) {
            return ajax($val['set'],-3);
        }
        if(!is_tel($val['tel'])) {
            return ajax('',6);
        }
        if(!is_email($val['email'])) {
            return ajax('',7);
        }
        try {
            $user_exist = Db::table('mp_user')->where('id',$val['uid'])->find();
            if(!$user_exist) {
                return ajax($val['uid'],-3);
            }
            $order_exist = Db::table('mp_order')->where('uid',$val['uid'])->find();
            if($order_exist) {
                if($order_exist['enter'] == 1) {
                    return ajax('',8);
                }
                $val['order_sn'] = $order_exist['order_sn'];
                $order_exist = true;
            }else {
                $order_exist = false;
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $cert = $val['cert'];
        if($cert) {
            if(!file_exists($cert)) {
                return ajax($cert,5);
            }
            $val['cert'] = $this->rename_file($cert);
        }else {
            unset($val['cert']);
        }

        $taocan = [
            '1'=>[
                'title' => '1元套餐',
                'price' => 1
            ],
            '2'=>[
                'title' => '2元套餐',
                'price' => 2
            ],
            '3'=>[
                'title' => '3元套餐',
                'price' => 3
            ]
        ];

        $val['title'] = $taocan[$val['set']]['title'];
        $val['price'] = $taocan[$val['set']]['price'];
        $val['openid'] = $user_exist['wechat_open_id'];
        try {
            if($order_exist) {
                $val['enter'] = 0;
                Db::table('mp_order')->where('uid',$val['uid'])->update($val);
            }else {
                $val['order_sn'] = create_unique_number('m');
                $val['create_time'] = time();
                Db::table('mp_order')->insert($val);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val);

    }




}