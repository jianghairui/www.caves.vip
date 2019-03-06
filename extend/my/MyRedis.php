<?php
namespace my;
class MyRedis
{
    protected static $handler = null;
    //创建静态私有的变量保存该类对象
    static private $instance;

    protected $options = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'select' => 0,
        'timeout' => 0,
        'expire' => 0,
        'persistent' => false,
        'prefix' => '',
    ];

    private function __construct($options = [])
    {
        if (!extension_loaded('redis')) {   //判断是否有扩展(如果你的apache没reids扩展就会抛出这个异常)
            throw new \BadFunctionCallException('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $func = $this->options['persistent'] ? 'pconnect' : 'connect';     //判断是否长连接
        self::$handler = new \Redis;
        self::$handler->$func($this->options['host'], $this->options['port'], $this->options['timeout']);

        if ('' != $this->options['password']) {
            self::$handler->auth($this->options['password']);
        }

        if (0 != $this->options['select']) {
            self::$handler->select($this->options['select']);
        }
    }

    static public function getInstance($config = []){
        //判断$instance是否是Uni的对象
        //没有则创建
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }
        return self::$instance;

    }

    /**
     * 写入缓存
     * @param string $key 键名
     * @param string $value 键值
     * @param int $exprie 过期时间 0:永不过期
     * @return bool
     */
    public static function set($key, $value, $exprie = 0)
    {
        if(is_object($value)||is_array($value)){
            $value = serialize($value);
        }

        if ($exprie == 0) {
            $set = self::$handler->set($key, $value);
        } else {
            $set = self::$handler->setex($key, $exprie, $value);
        }
        return $set;
    }

    /**
     * 读取缓存
     * @param string $key 键值
     * @return mixed
     */
    public static function get($key)
    {
        $fun = is_array($key) ? 'Mget' : 'get';
        $value = self::$handler->{$fun}($key);

        $value_serl = @unserialize($value);
        if(is_object($value_serl)||is_array($value_serl)){
            return $value_serl;
        }

        return $value;
    }

    /**
     * 获取值长度
     * @param string $key
     * @return int
     */
    public static function lLen($key)
    {
        return self::$handler->lLen($key);
    }

    /**
     * 将一个或多个值插入到列表头部
     * @param $key
     * @param $value
     * @return int
     */
    public static function lPush($key, $value)
    {
        if(is_object($value)||is_array($value)){
            $value = serialize($value);
        }
        return self::$handler->lPush($key, $value);
    }

    /**
     * 移出并获取列表的第一个元素
     * @param string $key
     * @return string
     */
    public static function lPop($key)
    {
        $value = self::$handler->lPop($key);
        $value_serl = @unserialize($value);
        if(is_object($value_serl)||is_array($value_serl)){
            return $value_serl;
        }
        return $value;
    }

    /**
     * 移出并获取列表的最后一个元素
     * @param string $key
     * @return string
     */
    public static function rPop($key)
    {
        $value = self::$handler->rPop($key);
        $value_serl = @unserialize($value);
        if(is_object($value_serl)||is_array($value_serl)){
            return $value_serl;
        }
        return $value;
    }


}