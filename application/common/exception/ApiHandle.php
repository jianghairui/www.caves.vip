<?php
/**
 * Created by PhpStorm.
 * User: sleep
 * Date: 2018/3/31
 * Time: 下午4:34
 */

namespace app\common\exception;

use Exception;
use think\exception\Handle;

class ApiHandle extends Handle
{
    function render(Exception $e)
    {
        return ajax($e->getMessage(), -1, 400);
    }
}