<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/15
 * Time: 20:30
 */
namespace app\admin\controller;

use think\Db;
class System extends Common {

    public function setting() {
        $info = Db::table('mp_setting')->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function settingMod() {
        $val['req_rate'] = input('post.req_rate');
        $val['withdraw_rate'] = input('post.withdraw_rate');
        $val['agency_rate'] = input('post.agency_rate');
        $val['minimum'] = input('post.minimum');
        $val['carriage'] = input('post.carriage');
        $val['credit'] = input('post.credit');
        $val['min_credit'] = input('post.min_credit');
        $this->checkPost($val);
        $val['allow_ip'] = input('post.allow_ip');
        $val['contact'] = input('post.contact');

        if(!is_currency($val['minimum'])) {
            return ajax('金额不可以低于1',-1);
        }

        if($val['allow_ip']) {
            $ips = explode(',',$val['allow_ip']);
            foreach ($ips as $v) {
                if(!filter_var($v,FILTER_VALIDATE_IP)) {
                    return ajax('ip不合法',-1);
                }
            }
        }
        $val['req_rate'] = $val['req_rate']/100;
        $val['withdraw_rate'] = $val['withdraw_rate']/100;
        $val['agency_rate'] = $val['agency_rate']/100;
        try {
            Db::table('mp_setting')->where('id','=',1)->update($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val);
    }

    public function syslog() {

        $param['logmin'] = input('param.logmin');
        $param['logmax'] = input('param.logmax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',20);

        $where = [];
        if($param['logmin']) {
            $where[] = ['s.create_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['logmin'])))];
        }

        if($param['logmax']) {
            $where[] = ['s.create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['logmax'])))];
        }

        if($param['search']) {
            $where[] = ['a.realname|s.detail','like',"%{$param['search']}%"];
        }
        $count = Db::table('mp_syslog')->alias('s')
            ->join('mp_admin a','s.admin_id=a.id','left')
            ->where($where)->count();
        $list = Db::table('mp_syslog')->alias('s')
            ->join('mp_admin a','s.admin_id=a.id','left')
            ->where($where)
            ->order(['create_time'=>'DESC'])
            ->field('s.*,a.realname,a.username')
            ->limit(($curr_page - 1)*$perpage,$perpage)
            ->select();
        $page['count'] = $count;
        $page['curr'] = $curr_page;
        $page['totalPage'] = ceil($count/$perpage);
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }



}