<?php

/**
 * Created by PhpStorm.
 * User: G-emall
 * Date: 2017/6/1
 * Time: 14:51
 */
class WebSocket
{
    public static $instance = null;
    private $application;
    private $host;
    private $port;
    private $type;
    public function __construct()
    {
        $this->host = "192.168.186.128";
        $this->port = 9502;
        //读取配置
        define('APPLICATION_PATH', dirname(dirname(__DIR__)));
        $this->application = new Yaf_Application(APPLICATION_PATH. "/conf/application.ini");
        $this->application->bootstrap();
        //创建websocket服务端
        $ws = new swoole_websocket_server($this->host,$this->port);

        $ws->set(
            array(
                'worker_num' => 4,
                'daemonize'   => false,
                'package_max_length' => 5 * 1024 * 1024,
                'buffer_output_size' => 4 * 1024 * 1024,
                'heartbeat_check_interval' => 30, //心跳检测
                'heartbeat_idle_time' => 60,//TCP连接的最大闲置时间，单位s , 如果某fd最后一次发包距离现在的时间超过heartbeat_idle_time会把这个连接关闭
            )
        );

        //监听WebSocket连接打开事件
        $ws->on('Open',array($this,'onOpen'));

        //监听WebSocket消息事件
        $ws->on('Message',array($this,'onMessage'));

        //监听WebSocket连接关闭事件
        $ws->on('Close',array($this,'onClass'));

        $ws->start();

    }

    public function onOpen($ws,$request){
        //echo $request->fd;
    }

    public function onMessage($ws,$frame){
        //echo "Message-: {$frame->data}\n";
        $data = json_decode($frame->data,true);
        $this->type = $data['type'];
        //检查此用户是否是系统用户

        //存储redis服务
        $redis = new RedisCache();
        $_setKey = $data['type'].'-'.$data['username'].'-'.$frame->fd;
        $_setValue = $frame->data;
        $setRes = $redis->_set($_setKey,$_setValue);

        switch ($this->type){
            case 'login':
                $message = array('username'=>$data['username']);
                $message['msg'] = $setRes == false ? '登陆失败' : '登陆成功';
                break;
            default:
                $message = array('msg' => 'error');
                break;

        }

        $ws->push($frame->fd,json_encode($message));
    }

    public function onClass($ws,$fd){
        echo "client-{$fd} is closed\n";
    }

    //实例化本类
    public static function getInstance(){
        if(self::$instance === null)
            self::$instance = new WebSocket();
        return self::$instance;
    }

}

WebSocket::getInstance();