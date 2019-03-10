<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/25
 * Time: 16:09
 */
namespace app\sh\controller;
use think\Db;
use think\Exception;
use EasyWeChat\Factory;
use think\exception\HttpResponseException;

class Member extends Common {

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    //会员列表
    public function memberlist() {
        $param['status'] = input('param.status','');
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];

        if(!is_null($param['status']) && $param['status'] !== '') {
            $where[] = ['status','=',$param['status']];
        }

        if($param['logmin']) {
            $where[] = ['create_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['logmin'])))];
        }

        if($param['logmax']) {
            $where[] = ['create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['logmax'])))];
        }

        if($param['search']) {
            $where[] = ['nickname|tel','like',"%{$param['search']}%"];
        }

        $count = Db::table('mp_user')->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $list = Db::table('mp_user')->where($where)->limit(($curr_page - 1)*$perpage,$perpage)->select();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('status',$param['status']);
        return $this->fetch();
    }
    //会员详情
    public function userdetail() {
        $id = input('param.id');
        $where[] = ['id','=',$id];
        try {
            $info = Db::table('mp_user')
                ->where($where)
                ->find();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $this->assign('info',$info);
        return $this->fetch();
    }
    //通过认证
    public function userPass() {
        $map[] = ['status','=',-1];
        $map[] = ['id','=',input('post.id',0)];

        $exist = Db::table('mp_user')->where($map)->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        Db::startTrans();
        try {
            Db::table('mp_user')->where($map)->setInc('credit',100);
            Db::table('mp_user')->where($map)->update(['status'=>1]);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //拒绝认证
    public function userReject() {
        $map[] = ['status','=',-1];
        $map[] = ['id','=',input('post.id',0)];

        $exist = Db::table('mp_user')->where($map)->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        try {
            Db::table('mp_user')->where($map)->update(['status'=>-2]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }
    //批量通过认证
    public function multiPass() {
        $map[] = ['status','=',-1];
        $id_array = input('post.check');
        if(empty($id_array)) {
            return ajax('请选择认证对象',-1);
        }
        $map[] = ['id','in',$id_array];

        Db::startTrans();
        try {
            Db::table('mp_user')->where($map)->setInc('credit',100);
            $res = Db::table('mp_user')->where($map)->update(['status'=>1]);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(),-1);
        }
        return ajax('共有' . $res . '条通过认证',1);
    }
    //拒绝通过认证
    public function multiReject() {
        $map[] = ['status','=',-1];
        $id_array = input('post.check');
        if(empty($id_array)) {
            return ajax('请选择认证对象',-1);
        }
        $map[] = ['id','in',$id_array];

        try {
            $res = Db::table('mp_user')->where($map)->update(['status'=>-2]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax('共有' . $res . '条未通过',1);
    }
    //拉黑用户
    public function userStop() {
        $id = input('post.id');
        $map[] = ['id','=',$id];
        try {
            $res = Db::table('mp_user')->where($map)->update(['status'=>2]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax([],1);
        }else {
            return ajax('拉黑失败',-1);
        }
    }
    //恢复用户
    public function userGetback() {
        $id = input('post.id');
        $map[] = ['status','=',2];
        $map[] = ['id','=',$id];
        try {
            $res = Db::table('mp_user')->where($map)->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax([],1);
        }else {
            return ajax('恢复失败',-1);
        }
    }
    //批量拉黑
    public function multiDel() {
        $map[] = ['status','<>',2];
        $id_array = input('post.check');
        if(empty($id_array)) {
            return ajax('请选择拉黑对象',-1);
        }
        $map[] = ['id','in',$id_array];

        try {
            $res = Db::table('mp_user')->where($map)->update(['status'=>2]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax('共拉黑' . $res . '个用户',1);
    }
    //获奖列表
    public function winnerlist() {
        $param['status'] = input('param.status','');
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];
        $where[] = ['a.win','=',1];

        if(!is_null($param['status']) && $param['status'] !== '') {
            $where[] = ['a.status','=',$param['status']];
        }

        if($param['logmin']) {
            $where[] = ['a.win_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['logmin'])))];
        }

        if($param['logmax']) {
            $where[] = ['a.win_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['logmax'])))];
        }

        if($param['search']) {
            $where[] = ['u.nickname|u.tel','like',"%{$param['search']}%"];
        }

        $count = Db::table('mp_prize_actor')->alias('a')
            ->join("mp_user u","a.openid=u.openid",'left')
            ->join("mp_prize p","a.prize_id=p.id",'left')->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $list = Db::table('mp_prize_actor')->alias('a')
            ->join("mp_user u","a.openid=u.openid",'left')
            ->join("mp_prize p","a.prize_id=p.id",'left')
            ->where($where)
            ->field('a.*,u.nickname,u.realname,u.avatar,u.tel,p.title')
            ->order(['a.win_time'=>'DESC'])
            ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('status',$param['status']);
        return $this->fetch();
    }
    //发货
    public function sendPrize() {
        $id = input('post.id');
        $map[] = ['id','=',$id];
        $map[] = ['status','=',0];
        try {
            $res = Db::table('mp_prize_actor')->where($map)->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax([],1);
        }else {
            return ajax('操作失败',-1);
        }
    }
    //查看充值列表
    public function recharge() {
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',20);

        $where = [];
        if($param['logmin']) {
            $where[] = ['p.pay_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['logmin'])))];
        }

        if($param['logmax']) {
            $where[] = ['p.pay_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['logmax'])))];
        }

        if($param['search']) {
            $where[] = ['p.order_sn|u.realname|u.tel|u.nickname|v.title','like',"%{$param['search']}%"];
        }
        $count = Db::table('mp_vip_pay')->alias('p')
            ->join('mp_user u','p.openid=u.openid','left')
            ->join('mp_vip v','p.vip_id=v.id','left')
            ->where($where)->count();
        $list = Db::table('mp_vip_pay')->alias('p')
            ->join('mp_user u','p.openid=u.openid','left')
            ->join('mp_vip v','p.vip_id=v.id','left')
            ->where($where)
            ->order(['p.pay_time'=>'DESC'])
            ->field('p.*,u.realname,u.nickname,u.tel,v.title')
            ->limit(($curr_page - 1)*$perpage,$perpage)
            ->select();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }

    //提现列表
    public function withdraw() {
        $param['status'] = input('param.status','');
        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];

        if(!is_null($param['status']) && $param['status'] !== '') {
            $where[] = ['w.status','=',$param['status']];
        }

        if($param['logmin']) {
            $where[] = ['w.apply_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['logmin'])))];
        }

        if($param['logmax']) {
            $where[] = ['w.apply_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['logmax'])))];
        }

        if($param['search']) {
            $where[] = ['w.order_sn|u.nickname|u.realname|u.tel','like',"%{$param['search']}%"];
        }

        $count = Db::table('mp_withdraw')->alias('w')->join('mp_user u','w.openid=u.openid','left')->where($where)->count();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $list = Db::table('mp_withdraw')->alias('w')
            ->join('mp_user u','w.openid=u.openid','left')
            ->where($where)
            ->field('w.*,u.realname,u.tel,u.avatar,u.nickname,u.balance')
            ->order(['apply_time'=>'DESC'])
            ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('status',$param['status']);
        return $this->fetch();
    }
    //通过提现审核
    public function withdrawPass() {
        $map[] = ['status','=',0];
        $map[] = ['id','=',input('post.id',0)];

        $exist = Db::table('mp_withdraw')->where($map)->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }

        try {
            $res = Db::table('mp_withdraw')->where($map)->update(['status'=>1,'check_time'=>time()]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        if($res > 0) {
            //todo 微信转账
            $tpl = ['openid'=>$exist['openid'],'form_id'=>$exist['form_id'],'order_sn'=>$exist['order_sn'],'money'=>$exist['real_money']];
            $this->asyn_tranfer($tpl);
            $this->asyn_send_passtpl($tpl);
            return ajax([],1);
        }else {
            return ajax('审核失败',-1);
        }
    }
    //拒绝提现审核
    public function withdrawReject() {
        $map[] = ['status','=',0];
        $map[] = ['id','=',input('post.id',0)];
        $reason = input('post.reason','系统余额不足');
        $exist = Db::table('mp_withdraw')->where($map)->find();
        if(!$exist) {
            return ajax('非法操作',-1);
        }
        try {
            $res = Db::table('mp_withdraw')->where($map)->update(['status'=>-1,'check_time'=>time()]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }

        if($res > 0) {
            $insert_data = [
                'detail' => '提现被拒',
                'money' => $exist['real_money'],
                'type' => 4,
                'create_time' => time(),
                'openid' => $exist['openid']
            ];
            Db::startTrans();
            try {
                Db::table('mp_user')->where('openid','=',$exist['openid'])->setInc('balance',$exist['real_money']);
                Db::table('mp_billing')->insert($insert_data);
                Db::commit();
            }catch (\Exception $e) {
                Db::rollback();
                return ajax('返钱失败',-1);
            }
            //todo 发送模板消息
            $tpl = ['reason'=>$reason,'openid'=>$exist['openid'],'form_id'=>$exist['form_id'],'order_sn'=>$exist['order_sn']];
            $this->asyn_send_rejecttpl($tpl);
            return ajax([],1);
        }else {
            return ajax('审核失败',-1);
        }
    }
    //转账
    public function transfer() {
        if($_SERVER['REMOTE_ADDR'] === '47.104.130.39') {
            $data = input('param.');
            $where = [
                ['order_sn', '=', $data['order_sn']],
                ['status', '=', 1],
            ];
            $exist = Db::table('mp_withdraw')->where($where)->find();
            if ($exist && $exist['openid'] == $data['openid']) {
                $app = Factory::payment($this->mp_config);
                $result = $app->transfer->toBalance([
                    'partner_trade_no' => $exist['order_sn'], // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
                    'openid' => $exist['openid'],
                    'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
                    're_user_name' => '', // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
                    'amount' => floatval($exist['real_money']) * 100, // 企业付款金额，单位为分
                    'desc' => '提现到账', // 企业付款操作说明信息。必填
                ]);
                $insert_data = [
                    'order_sn' => $data['order_sn'],
                    'detail' => json_encode($result)
                ];
                Db::table('mp_withdraw_pay')->insert($insert_data);
            } else {
                $data['info'] = '订单不存在';
                Db::table('mp_test')->insert(['detail' => json($data)]);
            }
        }

    }
    //发送通过审核通知
    public function sendPassTpl() {
        $data = input('param.');
        //下面两行为调试用
//        $data['cmd'] = request()->controller() . '/' . request()->action();
//        Db::table('mp_withdraw_pay')->insert(['detail'=>json_encode($data)]);
        try {
            $template_id = 'zUD8Lhpll8p3yl70xmuRM2mjsoVUlrEoIfqN7RObZM8';
            $touser = $data['openid'];
            $title = '提现申请';
            $status = '已通过,请注意查收';
            $time = date('Y-m-d H:i:s');
            $desc = $data['order_sn'];
            $form_id = $data['form_id'];
        }catch (\Exception $e) {
            throw new HttpResponseException(ajax($e->getMessage(),-1));
        }

        $app = Factory::miniProgram($this->mp_config);
        $result = $app->template_message->send([
            'touser' => $touser,
            'template_id' => $template_id,
            'page' => 'index',
            'form_id' => $form_id,
            'data' => [
                'keyword1' => $title,
                'keyword2' => $status,
                'keyword3' => $time,
                'keyword4' => $desc,
            ]
        ]);
//        Db::table('mp_test')->insert(['cmd'=>$data['cmd'],'detail'=>json_encode($result)]);
        if($result['errcode'] === 0) {
            Db::table('mp_withdraw')->where('order_sn','=',$data['order_sn'])->update(['send'=>1]);
        }else{
            Db::table('mp_withdraw')->where('order_sn','=',$data['order_sn'])->update(['send'=>-1]);
        }
    }
    //发送未通过审核通知
    public function sendRejectTpl() {
        $data = input('param.');
        try {
            $template_id = 'iZ_pPUdONBNHxSxIENbpYHV-kNrghW8zcKg8EfkuSqU';
            $touser = $data['openid'];
            $title = '提现申请';
            $status = '已拒绝';
            $reason = $data['reason'];
            $time = date('Y-m-d H:i:s');
            $desc = $data['order_sn'];
            $form_id = $data['form_id'];
        }catch (\Exception $e) {
            throw new HttpResponseException(ajax($e->getMessage(),-1));
        }
        $send_data = [
            'touser' => $touser,
            'template_id' => $template_id,
            'page' => 'index',
            'form_id' => strval($form_id),
            'data' => [
                'keyword1' => $title,
                'keyword2' => $status,
                'keyword3' => $reason,
                'keyword4' => $time,
                'keyword5' => $desc,
            ]
        ];

        $app = Factory::miniProgram($this->mp_config);
        $result = $app->template_message->send($send_data);
        if($result['errcode'] === 0) {
            Db::table('mp_withdraw')->where('order_sn','=',$data['order_sn'])->update(['send'=>1]);
        }else{
            Db::table('mp_withdraw')->where('order_sn','=',$data['order_sn'])->update(['send'=>-1]);
        }
    }

//----  创建异步任务  审核结果消息模板,支付通知
    private function asyn_send_passtpl($data) {
        $data = [
            'order_sn' => $data['order_sn'],
            'openid' => $data['openid'],
            'form_id' => $data['form_id']
        ];
        $param = http_build_query($data);
        $fp = fsockopen('ssl://' . $this->weburl, 443, $errno, $errstr, 20);
        if (!$fp){
            echo 'error fsockopen';
        }else{
            stream_set_blocking($fp,0);
            $http = "GET /admin/member/sendPassTpl?".$param." HTTP/1.1\r\n";
            $http .= "Host: ".$this->weburl."\r\n";
            $http .= "Connection: Close\r\n\r\n";
            fwrite($fp,$http);
            usleep(1000);
            fclose($fp);
        }
    }

    private function asyn_send_rejecttpl($data) {
        $data = [
            'order_sn' => $data['order_sn'],
            'openid' => $data['openid'],
            'form_id' => $data['form_id'],
            'reason' => $data['reason']
        ];
        $param = http_build_query($data);
        $fp = fsockopen('ssl://' . $this->weburl, 443, $errno, $errstr, 20);
        if (!$fp){
            echo 'error fsockopen';
        }else{
            stream_set_blocking($fp,0);
            $http = "GET /admin/member/sendRejectTpl?".$param." HTTP/1.1\r\n";
            $http .= "Host: ".$this->weburl."\r\n";
            $http .= "Connection: Close\r\n\r\n";
            fwrite($fp,$http);
            usleep(1000);
            fclose($fp);
        }
    }

    private function asyn_tranfer($data) {
        $data = [
            'order_sn' => $data['order_sn'],
            'openid' => $data['openid'],
        ];
        $param = http_build_query($data);
        $fp = fsockopen('ssl://' . $this->weburl, 443, $errno, $errstr, 20);
        if (!$fp){
            echo 'error fsockopen';
        }else{
            stream_set_blocking($fp,0);
            $http = "GET /admin/member/transfer?".$param." HTTP/1.1\r\n";
            $http .= "Host: ".$this->weburl."\r\n";
            $http .= "Connection: Close\r\n\r\n";
            fwrite($fp,$http);
            usleep(1000);
            fclose($fp);
        }
    }
//---  END ---











}