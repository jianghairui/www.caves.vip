<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/11
 * Time: 13:09
 */
namespace app\art\controller\api;

use think\Controller;
use think\Db;

class Test extends Controller {

    public function test() {
        Db::table('mp_user')->insert(['nickname'=>'DJ_1246']);
        $id = Db::table('mp_user')->getLastInsID();
        halt($id);
    }

}