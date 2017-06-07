<?php

//redis 控制类
class RedisCache
{
    private static  $_instance = null;
    private $host;
    private $port;

    public function __construct()
    {
        $config = Yaf_Registry::get("config")->toArray();
        $this->host = $config['redis']['default']['host'];
        $this->port = $config['redis']['default']['port'];
        self::getInstance($this->host,$this->port);
    }

    /*
     * 防止克隆
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /*
     * 实例化redis
     */
    private static function getInstance($host,$port){
        if(self::$_instance === null){
             self::$_instance = self::_getConnect($host,$port);
        }
            return self::$_instance;
    }

    /*
     * 链接redis
     */
    private static function _getConnect($host,$port){
        $redis = new Redis();
        $redis->connect($host,$port);
         return $redis;
    }

    /*
     * 设置存储的value
     */
    public function _set($key,$value,$exp=3000){
        if(empty($key) || empty($value)) return false;

        if(!self::$_instance->exists($key)){
            return self::$_instance->set($key,$value,$exp);
        }else
            return false;
    }

    /*
     * 获取key
     */
    public function _get($key){
        if(!$key || !self::$_instance->exists($key))  return false;
        return self::$_instance->get($key);
    }





}