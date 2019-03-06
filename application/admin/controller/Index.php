<?php
namespace app\admin\controller;
use my\Auth;
use think\Db;
use think\Exception;
use EasyWeChat\Factory;

class Index extends Common
{

    //首页
    public function index() {
        $auth = new Auth();
        $authlist = $auth->getAuthList(session('admin_id'));
        $this->assign('authlist',$authlist);
        return $this->fetch();
    }
    //分类列表
    public function catelist()
    {
        $map[] = ['pid','=',0];
        $list = Db::table('mp_cate')->where($map)->select();
        $count = count($list);
        $this->assign('catelist',$list);
        $this->assign('count',$count);
        return $this->fetch();
    }
    //子分类列表
    public function childlist()
    {
        $cate_id = input('param.cate_id') ? input('param.cate_id') : 0;
        $page['query'] = http_build_query(input('param.'));
        $curr_page = input('param.page',config('app.page'));
        $perpage = input('param.perpage',config('app.perpage'));

        $map[] = ['id','=',$cate_id];
        $exist = Db::table('mp_cate')->where($map)->find();
        if(!$exist) {
            $this->error('非法操作');
        }
        $where[] = ['pid','=',$cate_id];
        $count = Db::table('mp_cate')->where($where)->count();
        $page['count'] = $count;
        $page['totalPage'] = ceil($count/$perpage);

        $list = Db::table('mp_cate')->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        $page['curr'] = $curr_page;
        $this->assign('catelist',$list);
        $this->assign('page',$page);
        $this->assign('cate',$exist);

        return $this->fetch();
    }
    //添加分类页面
    public function cateadd() {
        $cate_id = input('param.cate_id') ? input('param.cate_id') : 0;
        if($cate_id == 0) {
            $this->assign('cate_name','顶级分类');
        }else {
            $where[] = ['id','=',$cate_id];
            $where[] = ['pid','=',0];
            $exist = Db::table('mp_cate')->where($where)->find();
            if(!$exist) {
                $this->error('非法操作');
            }
            $this->assign('cate_name',$exist['cate_name']);
        }
        $this->assign('cate_id',$cate_id);
        return $this->fetch();
    }
    //添加分类提交
    public function cateadd_post() {
        $val['cate_name'] = input('post.cate_name');
        $val['pid'] = input('post.pid');

        $this->checkPost($val);
        if($val['pid'] != 0) {
            $where[] = ['pid','=',0];
            $where[] = ['id','=',$val['pid']];
            $exist = Db::table('mp_cate')->where($where)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
        }
        $map[] = ['cate_name','=',$val['cate_name']];
        $map[] = ['pid','=',$val['pid']];
        if($this->checkExist('mp_cate',$map)) {
            return ajax('分类已存在',-1);
        }

        foreach ($_FILES as $k=>$v) {
            if($v['name'] == '') {
                unset($_FILES[$k]);
            }
        }
        if(!empty($_FILES)) {
            $info = $this->upload(array_keys($_FILES)[0]);
            if($info['error'] === 0) {
                $val['cover'] = $info['data'];
            }else {
                return ajax($info['msg'],-1);
            }
        }
        $res = Db::table('mp_cate')->insert($val);
        if($res) {
            return ajax([]);
        }else {
            if(isset($val['cover'])) {
                @unlink($val['cover']);
            }
            return ajax('添加失败',-1);
        }
    }
    //修改分类页面
    public function catemod() {
        $cate_id = input('param.cate_id') ? input('param.cate_id') : 0;
        $exist = Db::table('mp_cate')->where('id',$cate_id)->find();
        if(!$exist) {
            $this->error('非法操作');
        }
        $this->assign('cate',$exist);
        return $this->fetch();
    }
    //修改分类提交
    public function catemod_post() {
        $val['cate_name'] = input('post.cate_name');
        $val['id'] = input('post.cate_id');
        $this->checkPost($val);

        $exist = Db::table('mp_cate')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        if($this->checkExist('mp_cate',[
            ['cate_name','=',$val['cate_name']],
            ['id','<>',$val['id']]
        ])) {
            return ajax('分类已存在',-1);
        }
        foreach ($_FILES as $k=>$v) {
            if($v['name'] == '') {
                unset($_FILES[$k]);
            }
        }

        if(!empty($_FILES)) {
            $info = $this->upload(array_keys($_FILES)[0]);
            if($info['error'] === 0) {
                $val['cover'] = $info['data'];
            }else {
                return ajax($info['msg'],-1);
            }
        }

        $res = Db::table('mp_cate')->update($val);
        if($res !== false) {
            if(!empty($_FILES)) {
                @unlink($exist['cover']);
            }
            return ajax([]);
        }else {
            if(!empty($_FILES)) {
                @unlink($val['cover']);
            }
            return ajax('修改失败',-1);
        }
    }
    //删除分类
    public function cate_del() {
        $val['id'] = input('post.cate_id');
        $this->checkPost($val);
        $exist = Db::table('mp_cate')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        $model = model('Cate');
        if($exist['pid'] == 0) {
            $child_ids = Db::table('mp_cate')->where('pid','eq',$val['id'])->column('id');
            try {
                $model::destroy($child_ids);
            }catch (Exception $e) {
                return ajax($e->getMessage(),-1);
            }
        }
        try {
            $model::destroy($val['id']);
        }catch (Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        return ajax([],1);
    }
    //停用分类
    public function cate_stop() {
        $val['id'] = input('post.cate_id');
        $this->checkPost($val);
        $exist = Db::table('mp_cate')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_cate')->where('id',$val['id'])->update(['status'=>0]);
        if($res !== false) {
            if($exist['pid'] == 0) {
                Db::table('mp_cate')->where('pid',$val['id'])->update(['status'=>0]);
            }
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }
    //启用分类
    public function cate_start() {
        $val['id'] = input('post.cate_id');
        $this->checkPost($val);
        $exist = Db::table('mp_cate')->where('id',$val['id'])->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        $res = Db::table('mp_cate')->where('id',$val['id'])->update(['status'=>1]);
        if($res !== false) {
            if($exist['pid'] == 0) {
                Db::table('mp_cate')->where('pid',$val['id'])->update(['status'=>1]);
            }
            return ajax([],1);
        }else {
            return ajax([],-1);
        }
    }
    //查看需求列表
    public function rlist() {
        $param['status'] = input('param.status','');
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',config('app.page'));
        $perpage = input('param.perpage',10);

        $where = [];
        $where[] = ['r.pay_status','<>',0];

        if(!is_null($param['status']) && $param['status'] !== '') {
            $where[] = ['r.status','=',$param['status']];
        }

        if($param['logmin']) {
            $where[] = ['r.create_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['logmin'])))];
        }

        if($param['logmax']) {
            $where[] = ['r.create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['logmax'])))];
        }

        if($param['search']) {
            $where[] = ['r.order_sn|r.title|u.tel','like',"%{$param['search']}%"];
        }

        $count = Db::table('mp_req')->alias('r')
            ->join('mp_user u','r.f_openid=u.openid','left')
            ->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $list = Db::table('mp_req')->alias('r')
            ->join('mp_user u','r.f_openid=u.openid','left')
            ->join('mp_cate c','r.cate_id=c.id','left')
            ->field('r.*,u.nickname,u.realname,u.tel,c.cate_name')
            ->where($where)->order(['r.id'=>'DESC'])->limit(($curr_page - 1)*$perpage,$perpage)->select();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('status',$param['status']);
        return $this->fetch();
    }
    //查看需求详情
    public function detail() {
        $rid = input('param.rid');
        $info = Db::table('mp_req')->alias('r')
            ->join('mp_cate c','r.cate_id=c.id','left')
            ->where('r.id','=',$rid)
            ->field('r.*,c.cate_name')
            ->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    //需求审核-通过
    public function reqPass() {
        $map[] = ['status','=',0];
        $map[] = ['pay_status','=',1];
        $map[] = ['id','=',input('post.id',0)];

        $exist = Db::table('mp_req')->where($map)->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        try {
            Db::table('mp_req')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //需求审核-拒绝
    public function reqReject() {
        $map[] = ['status','=',0];
        $map[] = ['pay_status','=',1];
        $map[] = ['id','=',input('post.id',0)];

        $exist = Db::table('mp_req')->where($map)->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        try {
            Db::table('mp_req')->where($map)->update(['status'=>-1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        //todo 退款
        $arg = [
            'order_sn' => $exist['order_sn'],
            'reason' => '需求未通过审核'
        ];
        $this->asyn_refund($arg);
        return ajax([],1);
    }
    //需求审核-批量通过
    public function multiPass() {
        $map[] = ['status','=',0];
        $map[] = ['pay_status','=',1];
        $id_array = input('post.check');
        if(empty($id_array)) {
           return ajax('请选择审核对象',-1);
        }
        $map[] = ['id','in',$id_array];

        try {
            $res = Db::table('mp_req')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax('共有' . $res . '条通过审核',1);
    }
    //需求审核-批量拒绝
    public function multiReject() {
        $map[] = ['status','=',0];
        $map[] = ['pay_status','=',1];
        $id_array = input('post.check');
        if(empty($id_array)) {
            return ajax('请选择审核对象',-1);
        }
        $map[] = ['id','in',$id_array];
        $exist = Db::table('mp_req')->where($map)->find();
        try {
            $res = Db::table('mp_req')->where($map)->update(['status'=>-1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        //todo 退款
        foreach ($exist as $v) {
            $arg = [
                'order_sn' => $v['order_sn'],
                'reason' => '需求未通过审核'
            ];
            $this->asyn_refund($arg);
        }

        return ajax('共有' . $res . '条未通过',1);
    }
    //订单矛盾后台最终审核,算完成
    public function makeSuccessful() {
        $id = input('post.id');
        $map = [
            ['id','=',$id],
            ['status','=',3],
            ['pay_status','=',1]
        ];
        $exist = Db::table('mp_req')->where($map)->find();
        if(!$exist) {
            return ajax('订单不存在或状态已改变',-1);
        }
        $data['total_money'] = floatval($exist['order_price']);
        $data['a_money'] = floatval($exist['order_price']);
        $data['f_money'] = 0;
        Db::startTrans();
        try {
            $update_req = [
                'status' => 6,
                'finish_time' => time()
            ];
            $data['req_id'] = $id;
            $billing = [
                'req_id' => $id,
                'openid' => $exist['to_openid'],
                'detail' => '订单收入',
                'money' => $data['f_money'],
                'type' => 1,
                'create_time' => time()
            ];
            Db::table('mp_req')->where($map)->update($update_req);
            Db::table('mp_user')->where('openid','=',$exist['to_openid'])->setInc('balance',$data['a_money']);
            Db::table('mp_req_dispute')->insert($data);
            Db::table('mp_billing')->insert($billing);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
    //订单矛盾后台最终审核,算未完成
    public function makeFailed() {
        $id = input('post.id');
        $map = [
            ['id','=',$id],
            ['status','=',3],
            ['pay_status','=',1]
        ];
        $exist = Db::table('mp_req')->where($map)->find();
        if(!$exist) {
            return ajax('订单不存在或状态已改变',-1);
        }
        $data['money'] = floatval($exist['real_price']);
        $data['a_money'] = 0;
        $data['f_money'] = floatval($exist['real_price']);
        Db::startTrans();
        try {
            $update_req = [
                'status' => 6,
                'finish_time' => time()
            ];
            $data['req_id'] = $id;
            Db::table('mp_req')->where($map)->update($update_req);
            $setting = Db::table('mp_setting')->where('id','=',1)->find();
            $user = Db::table('mp_user')->where('to_openid','=',$exist['to_openid'])->find();
            if(($user['credit'] - $setting['credit']) <= $setting['min_credit']) {
                $update_user['status'] = 2;
            }
            $update_user['credit'] = $user['credit'] - $setting['credit'];
            Db::table('mp_user')->where('openid','=',$exist['to_openid'])->update($update_user);
            Db::table('mp_req_dispute')->insert($data);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(),-1);
        }
        $arg = [
            'order_sn' => $exist['order_sn'],
            'f_money' => $data['f_money'],
            'reason' => '订单退款'
        ];
        $this->asyn_dispute_refund($arg);
        return ajax();
    }
    //其他方案
    public function resolve_conflict() {
        $percent = input('post.percent');
        $id = input('post.id');
        if(!preg_match('/(^1$)|(^\d{1,2}$)/',$percent)) {
            return ajax('请输入正确数字',-1);
        }
        $map = [
            ['id','=',$id],
            ['status','=',3],
            ['pay_status','=',1]
        ];
        $exist = Db::table('mp_req')->where($map)->find();
        if(!$exist) {
            return ajax('订单不存在或状态已改变',-1);
        }
        $data['total_money'] = floatval($exist['order_price']);
        $data['a_money'] = round($exist['order_price']*$percent/100,2);
        $data['f_money'] = $data['total_money'] - $data['a_money'];
        return ajax($data);
    }

    public function resolve_conflict_post() {
        $percent = input('post.percent');
        $id = input('post.id');
        if(!preg_match('/(^1$)|(^\d{1,2}$)/',$percent)) {
            return ajax('请输入正确数字',-1);
        }
        $map = [
            ['id','=',$id],
            ['status','=',3],
            ['pay_status','=',1]
        ];
        $exist = Db::table('mp_req')->where($map)->find();
        if(!$exist) {
            return ajax('订单不存在或状态已改变',-1);
        }
        $data['money'] = floatval($exist['order_price']);
        $data['a_money'] = round($exist['order_price']*$percent/100,2);
        $data['f_money'] = $data['money'] - $data['a_money'];
        //todo 更改订单状态,转账退款
        Db::startTrans();
        try {
            $update_req = [
                'status' => 6,
                'finish_time' => time()
            ];
            $data['req_id'] = $id;
            $billing = [
                'req_id' => $id,
                'openid' => $exist['to_openid'],
                'detail' => '订单收入',
                'money' => $data['f_money'],
                'type' => 1,
                'create_time' => time()
            ];
             Db::table('mp_req')->where($map)->update($update_req);
             Db::table('mp_user')->where('openid','=',$exist['to_openid'])->setInc('balance',$data['a_money']);
             Db::table('mp_req_dispute')->insert($data);
             Db::table('mp_billing')->insert($billing);
             Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(),-1);
        }
        $arg = [
            'order_sn' => $exist['order_sn'],
            'f_money' => $data['f_money'],
            'reason' => '订单退款'
        ];
        $this->asyn_dispute_refund($arg);
        return ajax();
    }







    public function reqShow() {
        $map[] = ['id','=',input('post.id',0)];
        try {
            Db::table('mp_req')->where($map)->update(['show'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    public function reqHide() {
        $map[] = ['id','=',input('post.id',0)];
        try {
            Db::table('mp_req')->where($map)->update(['show'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }





//创建异步退款任务
    private function asyn_refund($arg,$type=0) {
        $data = [
            'order_sn' => $arg['order_sn'],
            'reason' => $arg['reason'],
        ];
        $cmd = $type ? 'wx_cancel_refund' : 'wx_refund';
        $param = http_build_query($data);
        $fp = fsockopen('ssl://' . $this->weburl, 443, $errno, $errstr, 20);
        if (!$fp){
            echo 'error fsockopen';
        }else{
            stream_set_blocking($fp,0);
            $http = "GET /index/plan/".$cmd."?".$param." HTTP/1.1\r\n";
            $http .= "Host: ".$this->weburl."\r\n";
            $http .= "Connection: Close\r\n\r\n";
            fwrite($fp,$http);
            usleep(1000);
            fclose($fp);
        }
    }
//创建部分退款任务
    private function asyn_dispute_refund($arg) {
        $data = [
            'order_sn' => $arg['order_sn'],
            'reason' => $arg['reason'],
            'refund' => $arg['f_money']
        ];
        $param = http_build_query($data);
        $fp = fsockopen('ssl://' . $this->weburl, 443, $errno, $errstr, 20);
        if (!$fp){
            echo 'error fsockopen';
        }else{
            stream_set_blocking($fp,0);
            $http = "GET /index/plan/wx_dispute_refund?".$param." HTTP/1.1\r\n";
            $http .= "Host: ".$this->weburl."\r\n";
            $http .= "Connection: Close\r\n\r\n";
            fwrite($fp,$http);
            usleep(1000);
            fclose($fp);
        }
    }








}
