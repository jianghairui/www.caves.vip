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
        include ROOT_PATH . '/extend/phpqrcode/phpqrcode.php';
        $value = '1';

        $errorCorrectionLevel = "L"; // 纠错级别：L、M、Q、H
        $matrixPointSize = "6"; // 点的大小：1到10
        header('Content-Type:image/png');
        \QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize);
        exit;
    }

}