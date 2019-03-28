<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/28
 * Time: 12:27
 */
$url = urlencode('https://www.caves.vip/get.php?name=jianghairui&lib=南京博物馆');
echo $url;die();
header('Location:' . $url);
exit();