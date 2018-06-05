<?php
namespace app\game_api\controller;
use think\Controller;
use think\Session;

/**
 * 接口基类，封装一些常用方法
 * Class Base
 * @package app\game_api\controller
 */
class Base extends Controller
{
    private static $redis=null;

    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();
        if (self::$redis==null){
            self::$redis = new \Redis();
            self::$redis->connect('127.0.0.1',6379,60) or die('redis连接失败！');
            //self::$redis->auth('20160601'); //如果有设置密码，则需要连接密码
            //self::$redis->select(0); //选择缓存库
        }
    }

    /**
     * 接口回调信息
     * @param int $code 状态码
     * @param string $msg  提示信息
     * @param array $info  返回数据信息
     */
    protected function apiInfo($code=200,$msg='',$info=array())
    {
        return json(['code' => $code, 'message' => $msg, 'info'  => $info]);
    }

    /**
     * 获取$_GET参数
     * @param null $name
     * @param null $default
     * @return null
     */
    protected function g($name = null, $default = null)
    {
        if ( $name === null )
        {
            return $_GET;
        }

        return isset($_GET[$name]) ? $this->strToUtf8(trim($_GET[$name])) : $default;
    }

    /**
     * 获取$_POST参数
     * @param null $name
     * @param null $default
     * @return null
     */
    protected function p($name = null, $default = null)
    {
        if ( $name === null )
        {
            return $_POST;
        }
        return isset($_POST[$name]) ? $this->strToUtf8(trim($_POST[$name])) : $default;
    }

    /**
     * 判断是否是utf8编码并转换
     */
    protected function strToUtf8($str)
    {
        if (mb_detect_encoding($str, 'UTF-8', true) === false) {
            $str = utf8_encode($str); //判断是否为utf8编码
        }
        return $str;
    }


    /**
     * 设置session
     * 用redis缓存，更好地设置刷新过期时间
     */
    protected function setSession($name = '', $value='',$expire=7200)
    {
        if(!empty($name) && !empty($value)){
           return $this->setRedis('session:'.$name,$value,$expire);
        }
        return false;
    }

    /**
     * 获取session
     */
    protected function getSession($name = '')
    {
        return $this->getRedis('session:'.$name);
    }

    /**
     * 删除session
     */
    protected function delSession($name='')
    {
        return $this->delRedis('session:'.$name);
    }

    /**
     * 刷新session过期时间
     */
    protected function flushSession($name = '',$expire=7200)
    {
        $redis = self::$redis;
        $session = $redis->get('session:'.$name);
        if($session){
            $redis->expire('session:'.$name,$expire);
            return true;
        }
        return false;
    }

    /**
     * 刷新token时间
     */
    protected function flushTokenExpire($key='',$expire=7200)
    {
        $redis = self::$redis;
        if(!empty($key)){
            $redis->expire($key,$expire);
            return true;
        }
        return false;
    }

    /**
     * 设置redis字符串缓存
     */
    protected function setRedis($key = '', $value='', $expire = 0)
    {
        if(!empty($key)){
            $redis = self::$redis;
            $redis->set($key,$value);
            if(!empty($expire)){
                $redis->expire($key,$expire);
            }
            return true;
        }
        return flase;
    }

    /**
     * 获取redis字符串缓存
     */
    protected function getRedis($key = '')
    {
        $redis = self::$redis;
        $value = $redis->get($key);
        return $value;
    }

    /**
     * 设置hash缓存
     */
    protected function setHash($key = '', $arr=[], $expire = 0)
    {
        if(!empty($key) && !empty($arr)){
            $redis = self::$redis;
            $redis->hmset($key,$arr);
            if(!empty($expire)){
                $redis->expire($key,$expire);
            }
            return true;
        }
        return flase;
    }

    /**
     * 获取hash缓存
     */
    protected function getHash($key = '',$arr=[])
    {
        $redis = self::$redis;
        $value = null;
        if(!empty($key)){
            if(!empty($arr)){
                $value = $redis->hmget($key,$arr);
            }else{
                $value = $redis->hgetall($key);//不指定字段数组则获取所有
            }
        }
        return $value;
    }

    /*
     * 给hash添加键值
     */
    protected function HashAdd($key='',$k='',$v='')
    {
        $redis = self::$redis;
        return $redis->Hsetnx($key,$k,$v); //成功返回1，否则返回0
    }

    /**
     * 删除单个缓存
     */
    protected function delRedis($key='')
    {
        return self::$redis->del($key);
    }


    /**
     * 验证用户是否登录或非法操作
     */
    protected function verifyUser()
    {
        $user_id = $this->p('userId',1);
        $uid = $this->getSession('user_id'.$user_id);
        if(!$uid){
            return  $this->apiInfo(0,'请先登录！');
        }
        $token = $this->getRedis('token:'.$uid);
        //$token = $this->p('token','');
        if($token){
            $sessionFlush = $this->flushSession('user_id'.$user_id,7200);
            $tokenFlush = $this->flushTokenExpire('token:'.$uid);
        }else{
            return $this->apiInfo(110,'非法操作！');
        }
        return $uid;
    }


    /**
     * 保存平台id下对应的用户id
     */
    protected function setPlatId($platId='',$userId=[])
    {
        if(!empty($platId) && !empty($userId)){
            $data = $this->getHash('platId:'.$platId);
            if($data==null){
                $data = $userId;
            }else{
                $data = array_unique(array_merge($data,$userId));
            }
            if($this->setHash('platId:'.$platId,$data)){
                return true;
            }
        }
        return false;
    }

    /**获取网关信息
     * @param string $gate_id
     * @return array|bool|mixed
     */
    protected function gateInfo($key='00100',$gate_id='')
    {
        if(!empty($gate_id)){ //获取单条信息
            $info = self::$redis->hget($key,$gate_id);
            return unserialize($info);
        }else{
            $res = self::$redis->hgetall($key);
            $data = [];
            foreach ($res as $key=>$value){
                $data[] = unserialize($value);
            }
            return $data;
        }
    }

    /**获取连接数量最小的网关信息
     * @return mixed|string
     */
    protected function gateMin()
    {
        $gateInfo = $this->gateInfo();
        if($gateInfo){
            $gate = array_column($gateInfo,NULL,'gateNum');
            //$num = min($gate);
            ksort($gate);
            $data = array_shift($gate);
            return ['gatePort'=>$data['gatePort'],'gateServer'=>$data['gateServer']];
        }
        return '信息失效！';
    }

    /**
     * 返回redis对象
     */
    protected function redisObj()
    {
        return self::$redis;
    }
}
