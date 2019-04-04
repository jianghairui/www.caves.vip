<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/11
 * Time: 13:09
 */
namespace app\api\controller;

use think\Controller;
use think\Db;

class Test extends Controller {

    public function test() {
//        $req_ids = Db::table('mp_design_works')->where([
//            ['uid','=',2],
//            ['type','=',2]
//        ])->column('req_id');
//        var_dump($req_ids);
//        $avatar = 'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTIxSsoFRspPAxia19AQqEauZuWJsnuhWdl7Q0kRSqSPRPR9AM0y31A3LZLsBBDsTic5WxmGHMoAFZiaw/132';
//        if (substr($avatar,0,4) == 'http') {
//            echo 'YES';
//        }else {
//            echo 'NO';
//        }
        var_dump((0.2 - 0.1) == (0.1));
    }

}