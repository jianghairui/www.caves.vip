<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/4/12
 * Time: 10:23
 */
namespace app\api\controller;
use think\Db;
use EasyWeChat\Factory;
class Activity extends Common {

    public function activityList() {
        $list = [
            [
                'id' => 1,
                'cover' => 'static/uploads/activity/test.png',
                'title' => '文创代言现金红包',
                'end' => 0,
            ],
//            [
//                'id' => 2,
//                'cover' => 'static/uploads/activity/test.jpg',
//                'title' => '集齐卡片获精美礼品',
//                'end' => 0,
//            ],
//            [
//                'id' => 3,
//                'cover' => 'static/uploads/activity/test.jpg',
//                'title' => '揭开文创面纱',
//                'end' => 0,
//            ]
        ];
        return ajax($list);
    }

    public function getQrcode() {
        $uid = $this->myinfo['uid'];
        $app = Factory::miniProgram($this->mp_config);
        $response = $app->app_code->getUnlimit($uid, [
            'page'  => 'pages/auth/auth',
            'width' => '300'
        ]);
        $png = $uid . '.png';
        $save_path = 'static/uploads/appcode/';
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->saveAs($save_path, $png);
        }else {
            return ajax($response,-1);
        }
        return ajax($save_path . $png);
    }

    public function getInviteList() {
        try {
            $where = [
                ['i.inviter_id','=',$this->myinfo['uid']]
            ];
            $list = Db::table('mp_user')->alias('u')
                ->join("mp_invite i","u.id=i.to_uid","left")
                ->where($where)
                ->field("u.nickname,i.*")
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $rule = "新手任务中有多个任务，好友需根据要求，完成所有任务并全部获得奖励后，才会被计算为一个有效好友。";
        $data['list'] = $list;
        $data['rule'] = $rule;
        return ajax($data);
    }



}