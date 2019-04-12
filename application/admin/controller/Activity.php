<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/4/12
 * Time: 9:53
 */
namespace app\admin\controller;

class Activity extends Common {

    public function activityList() {
        return $this->fetch();
    }


}