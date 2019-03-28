<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/28
 * Time: 12:28
 */
//var_dump(urldecode($_SERVER['HTTP_HOST'] .));

echo urldecode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
