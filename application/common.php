<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\exception\HttpResponseException;
// 应用公共文件
function ajax($data = [], $code = 1, $httpCode = 200, $header = [])
{
    $ret = [
        'message' => config('code.' . $code),
        'code' => $code,
        'data' => $data,
    ];
    if (config('app_debug')) {
        $ret['cmd'] = request()->controller() . '/' . request()->action();
    }

    $header['X-Powered-By'] = config('app_name');
//    $header['origin'] = json_encode(request()->subDomain());
    header('Access-Control-Allow-Origin: *');
    return json()->data($ret)->code($httpCode)->header($header);
}

function input_limit($str,$limit_num,$char='utf8') {
    if(mb_strlen($str,$char) > $limit_num) {
        return false;
    }
    return true;
}

function if_int($str) {
    if(!preg_match('/^\d{1,10}$/',$str)) {
        return false;
    }
    return true;
}

function is_tel($tel) {
    if(!preg_match('/^1\d{10}$/',$tel)) {
        return false;
    }
    return true;
}

function is_email($email) {
    if(!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',$email)) {
        return false;
    }
    return true;
}

function is_currency($str) {
    if(!preg_match('/^\d{1,8}(\.\d{1,2})?$/',$str)) {
        return false;
    }
    return true;
}

function is_lonlat($lon='',$lat='') {
    $lon_rule = '/^-?((\d|[1-9]\d|1[0-7]\d)(\.\d{1,6})?|180)$/';
    $lat_rule = '/^-?((\d|[1-8]\d)(\.\d{1,6})?|90)$/';
    if(preg_match($lon_rule,$lon) && preg_match($lat_rule,$lat)) {
        return true;
    }
    return false;
}

function is_date($date, $formats = array("Y-m-d", "Y/m/d","Y-m-d H:i","Y-m-d H:i:s")) {
    $unixTime = strtotime($date);
    if (!$unixTime) { //strtotime转换不对，日期格式显然不对。
        return false;
    }
    //校验日期的有效性，只要满足其中一个格式就OK
    foreach ($formats as $format) {
        if (date($format, $unixTime) == $date) {
            return true;
        }
    }
    return false;
}

function isCreditNo_simple($vStr){
    $vCity = array(
        '11', '12', '13', '14', '15', '21', '22',
        '23', '31', '32', '33', '34', '35', '36',
        '37', '41', '42', '43', '44', '45', '46',
        '50', '51', '52', '53', '54', '61', '62',
        '63', '64', '65', '71', '81', '82', '91'
    );
    if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;
    if (!in_array(substr($vStr, 0, 2), $vCity)) return false;
    $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
    $vLength = strlen($vStr);
    if ($vLength == 18) {
        $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
    } else {
        $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
    }     if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
    if ($vLength == 18) {
        $vSum = 0;
        for ($i = 17; $i >= 0; $i--) {
            $vSubStr = substr($vStr, 17 - $i, 1);
            $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
        }
        if ($vSum % 11 != 1) return false;
    }     return true;
}

function genCode($length=6) {
    $key='';
    $pattern='1234567890';
    for($i=0;$i<$length;++$i)
    {
        $key .= $pattern[mt_rand(0,9)]; // 生成php随机数
    }
    return $key;
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;

    // 密匙
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);

    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length):
        substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
//解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
        sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        // 验证数据有效性，请看未加密明文的格式
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
            substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}

function randomkeys($length) {
    $returnStr='';
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for($i = 0; $i < $length; $i ++) {
        $returnStr .= $pattern {mt_rand ( 0, 61 )};
    }
    return $returnStr;
}

function gen_unique_number($letter = '')
{
    $time = explode (" ", microtime ());
    $timeArr = explode('.',$time [0]);
    $mtime = array_pop($timeArr);
    $fulltime = $letter.$time[1].$mtime;
    return $fulltime;
}

function create_unique_number($letter = '')
{
    $time = explode (" ", microtime ());
    $timeArr = explode('.',$time [0]);
    $mtime = array_pop($timeArr);
    $fulltime = $letter.$time[1].$mtime . mt_rand(100,999);
    return $fulltime;
}

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
        imagettftext($image,$fontsize,mt_rand(-30,30),$i*($width/$length)+5,mt_rand(($height/2)+($fontsize/2),($height/2)+($fontsize/2)),randColor($image),'static/src/fonts/ELEGGARI.TTF',$str[$i]);
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


/*----------------------------- 后加入的分割线--------------------------*/

function curl_post_data($url, $curlPost,$userCert = false)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if($userCert == true){
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT, config('cert_path'));
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY, config('key_path'));
    }
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    curl_setopt($ch, 1, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $data = curl_exec($ch);
    return $data;
}

//生成签名
function getSign($arr) {
    //去除数组中的空值
    $arr = array_filter($arr);
    //如果数组中有签名删除签名
    if(isset($arr['sing']))
    {
        unset($arr['sing']);
    }
    //按照键名字典排序
    ksort($arr);
    //生成URL格式的字符串
    $str = http_build_query($arr)."&key=" . config('mch_key');
    $str = urldecode($str);
    return  strtoupper(md5($str));
}

function xml2array($xml) {
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $values;
}
//单层转
function array2xml($arr) {
    if(!is_array($arr) || count($arr) <= 0) {
        return false;
    }
    $xml = "<xml>";
    foreach ($arr as $key=>$val)
    {
        if (is_numeric($val)){
            $xml.="<".$key.">".$val."</".$key.">";
        }else{
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
    }
    $xml.="</xml>";
    return $xml;
}
//递归转
function arr2xml($data, $root = true){
    $str="";
    if($root)$str .= "<xml>";
    foreach($data as $key => $val){
        //去掉key中的下标[]
        $key = preg_replace('/\[\d*\]/', '', $key);
        if(is_array($val)){
            $child = arr2xml($val, false);
            $str .= "<$key>$child</$key>";
        }else{
            $str.= "<$key><![CDATA[$val]]></$key>";
        }
    }
    if($root)$str .= "</xml>";
    return $str;
}

//后台上传图片
function upload($k,$base_path = 'upload/admin/') {
    if(checkfile($k) !== true) {
        return array('error'=>1,'msg'=>checkfile($k));
    }
    $filename_array = explode('.',$_FILES[$k]['name']);
    $ext = array_pop($filename_array);

    $path = $base_path . date('Y-m-d');
    is_dir($path) or mkdir($path,0755,true);
    //转移临时文件
    $newname = create_unique_number() . '.' . $ext;
    move_uploaded_file($_FILES[$k]["tmp_name"], $path . "/" . $newname);
    $filepath = $path . "/" . $newname;

    return array('error'=>0,'data'=>$filepath);
}
//检验格式大小
function checkfile($file) {
    $allowType = array(
        "image/gif",
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/pjpeg",
        "image/bmp"
    );
    if($_FILES[$file]["type"] == '') {
        return '图片存在中文名或超过2M';
    }
    if(!in_array($_FILES[$file]["type"],$allowType)) {
        return '图片格式无效';
    }
    if($_FILES[$file]["size"] > 1024*512) {
        return '图片大小不超过512Kb';
    }
    if ($_FILES[$file]["error"] > 0) {
        return "error: " . $_FILES[$file]["error"];
    }else {
        return true;
    }
}
//接口上传图片
function ajaxUpload($k,$maxsize=512) {
    $allowType = array(
        "image/gif",
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/pjpeg",
        "image/bmp"
    );
    if($_FILES[$k]["type"] == '') {
        throw new HttpResponseException(ajax('图片存在中文名或超过2M',20));
    }
    if(!in_array($_FILES[$k]["type"],$allowType)) {
        throw new HttpResponseException(ajax('文件类型不符' . $_FILES[$k]["type"],21));
    }
    if($_FILES[$k]["size"] > $maxsize*1024) {
        throw new HttpResponseException(ajax('图片大小不超过'.$maxsize.'Kb',22));
    }
    if ($_FILES[$k]["error"] > 0) {
        throw new HttpResponseException(ajax("error: " . $_FILES[$k]["error"],-1));
    }

    $filename_array = explode('.',$_FILES[$k]['name']);
    $ext = array_pop($filename_array);

    $path =  'tmp/';
    is_dir($path) or mkdir($path,0755,true);
    //转移临时文件
    $newname = create_unique_number() . '.' . $ext;
    move_uploaded_file($_FILES[$k]["tmp_name"], $path . $newname);
    $filepath = $path . $newname;
    return $filepath;
}

function rename_file($tmp,$path = '') {
    $filename = substr(strrchr($tmp,"/"),1);
    $path = $path ? $path : 'upload/api/';
    $path.= date('Y-m-d') . '/';
    is_dir($path) or mkdir($path,0755,true);
    @rename($tmp, $path . $filename);
    return $path . $filename;
}

function checkInput($postArray) {
    if(empty($postArray)) {
        throw new HttpResponseException(ajax('数据不能为空',-3));
    }
    foreach ($postArray as $value) {
        if (is_null($value) || $value === '') {
            throw new HttpResponseException(ajax('数据不能为空',-3));
        }
    }
    return true;
}

function checkPost($postArray) {
    if(empty($postArray)) {
        throw new HttpResponseException(ajax($postArray,-2));
    }
    foreach ($postArray as $value) {
        if (is_null($value) || $value === '') {
            throw new HttpResponseException(ajax($postArray,-2));
        }
    }
    return true;
}



