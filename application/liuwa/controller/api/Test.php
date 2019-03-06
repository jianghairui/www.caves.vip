<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/6
 * Time: 22:47
 */
namespace app\liuwa\controller\api;

class Test extends Base {

    public function index() {
        echo $this->cmd;
        halt('LALAS D');
    }

}