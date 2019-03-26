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
        $req_ids = Db::table('mp_design_works')->where([
            ['uid','=',2],
            ['type','=',2]
        ])->column('req_id');
        var_dump($req_ids);
    }

}