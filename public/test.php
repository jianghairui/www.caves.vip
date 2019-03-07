<?php
$code = generateVerify(200,50,2,4,24);


function generateVerify($width,$height,$type,$length,$fontsize) {
    $image = imagecreatetruecolor($width,$height);
    $white = imagecolorallocate($image,255,255,255);
    imagefilledrectangle($image,0,0,$width,$height,$white);
    //匹配验证码字符类型
    switch($type) {
        case 0:
            $str = join('',array_rand(range(0,9),$length));
            break;
        case 1:
            $str = join('',array_rand(array_flip(array_merge(range('a','z'),range('A','Z'))),$length));
            break;
        case 2:
            $str = join('',array_rand(array_flip(array_merge(range('a','z'),range('A','Z'),range(0,9))),$length));
            break;
    }
    for($i=0;$i<$length;$i++) {
        imagettftext($image,$fontsize,mt_rand(-30,30),$i*($width/$length)+5,mt_rand(($height/2)+($fontsize/2),($height/2)+($fontsize/2)),randColor($image),'static/src/fonts/PingFang-Regular.ttf',$str[$i]);
    }
    //添加像素点
    for ($i=1;$i<=100;$i++) {
        imagesetpixel($image,mt_rand(0,$width),mt_rand(0,$height),randColor($image));
    }
    //输出后销毁图片
    header('Content-Type:image/png');
    imagepng($image);
    imagedestroy($image);
    return $str;
}

function randColor($image) {
    return imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
}
?>