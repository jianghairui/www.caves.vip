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
                'cover' => 'static/uploads/activity/test.jpg',
                'title' => '文创代言现金红包',
                'end' => 0,
            ],
            [
                'id' => 2,
                'cover' => 'static/uploads/activity/test.jpg',
                'title' => '集齐卡片获精美礼品',
                'end' => 0,
            ],
            [
                'id' => 3,
                'cover' => 'static/uploads/activity/test.jpg',
                'title' => '揭开文创面纱',
                'end' => 0,
            ]
        ];
        return ajax($list);
    }

    public function getQrcode() {
        $uid = $this->myinfo['uid'];
        $app = Factory::miniProgram($this->mp_config);
        $response = $app->app_code->getUnlimit('inviter_id=' . $uid, [
            'page'  => 'pages/notes/notes',
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

}