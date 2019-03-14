<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/9/18
 * Time: 21:36
 */
namespace app\sh\controller\api;
use think\Controller;
use think\Db;
use think\exception\HttpResponseException;
class Common extends Controller {

    protected $cmd = '';
    protected $domain = '';
    protected $weburl = '';
    protected $mp_config = '';
    protected $myinfo = [];

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->cmd = request()->controller() . '/' . request()->action();
        $this->domain = 'www.caves.vip';
        $this->weburl = 'https://www.caves.vip/';
        $this->mp_config = [
            'app_id' => 'wx50f030c7f4a897ff',
            'secret' => '608b9a4b5cfeecfb5e7f83becc252db5',

            'mch_id'             => '1490402642',
            'key'                => 'TIANJINTAOCIYUAN20190111SHWHCOPY',   // API 密钥
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          =>  '/mnt/www.caves.vip/public/cert/apiclient_cert.pem',
            'key_path'           =>  '/mnt/www.caves.vip/public/cert/apiclient_key.pem',
            // 下面为可选项,指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => APP_PATH . '/wechat.log',
            ],
        ];
        $this->checkSession();
    }

    private function checkSession() {
        $noneed = [
            'Api.api/userauth'
        ];
        if (!in_array(request()->controller() . '/' . request()->action(), $noneed)) {
            return true;
        }else {
            $token = input('post.token');
            if(!$token) {
                throw new HttpResponseException(ajax('invalid token',-3));
            }
            try {
                $exist = Db::table('mp_token')->where([
                    ['token','=',$token],
                    ['end_time','>',time()]
                ])->find();
            }catch (\Exception $e) {
                throw new HttpResponseException(ajax($e->getMessage(),-1));
            }
            if($exist) {
                $this->myinfo = unserialize($exist['value']);
                return true;
            }else {
                throw new HttpResponseException(ajax('invalid token',-3));
            }
        }

    }

    protected function checkPost($postArray) {
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

    protected function xml2array($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    /**
     * 工具方法，将一个数组转成 xml 格式
     */
    protected function array2xml($arr) {
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

    protected function upload($k) {
        if($this->checkfile($k) !== true) {
            return array('error'=>1,'msg'=>$this->checkfile($k));
        }

        $filename_array = explode('.',$_FILES[$k]['name']);
        $ext = array_pop($filename_array);

        $path =  'static/tmp/';
        is_dir($path) or mkdir($path,0755,true);
        //转移临时文件
        $newname = create_unique_number() . '.' . $ext;
        move_uploaded_file($_FILES[$k]["tmp_name"], $path . $newname);
        $filepath = $path . $newname;

        return array('error'=>0,'data'=>$filepath);
    }

    protected function rename_file($tmp,$path = '') {
        $filename = substr(strrchr($tmp,"/"),1);
        $path = $path ? $path : 'static/uploads/note/';
        $path.= date('Y-m-d') . '/';
        is_dir($path) or mkdir($path,0755,true);
        @rename($tmp, $path . $filename);
        return '/' . $path . $filename;
    }

    //检验格式大小
    private function checkfile($file) {
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
            return '图片格式无效' . $_FILES[$file]["type"];
        }
        if($_FILES[$file]["size"] > 1024*1024) {
            return '图片大小不超过1024Kb';
        }
        if ($_FILES[$file]["error"] > 0) {
            return "error: " . $_FILES[$file]["error"];
        }else {
            return true;
        }
    }
    //生成签名
    protected function getSign($arr)
    {
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
        $str = http_build_query($arr)."&key=".$this->mp_config['key'];
        $str = $this->arrToUrl($str);
        return  strtoupper(md5($str));
    }
    //URL解码为中文
    protected function arrToUrl($str)
    {
        return urldecode($str);
    }

    protected function log($cmd,$str) {
        $file= ROOT_PATH . '/exception_api.txt';
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

    protected function paylog($cmd,$str) {
        $file= ROOT_PATH . '/notify.txt';
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }


}