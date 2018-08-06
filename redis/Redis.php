<?php
namespace redis;
/**
 * Redis扩展类(放入extend目录下，新建文件夹redis)
 */
class Redis 
{
	private static $redis = null;
	/**
	 * [__construct初始化]
	 */
	public function __construct()
	{
		if(!extension_loaded('redis')){
			exit('服务器不支持redis扩展!');
		}
		if(self::$redis==null){
			self::$redis = new \Redis();
            self::$redis->connect('127.0.0.1',6379,60) or die('redis连接失败！');
            //self::$redis->auth('20160601'); //如果有设置密码，则需要连接密码
            //self::$redis->select(0); //选择缓存库
		}
	}


	/**
	 * [添加缓存字段]
	 * @param [string] $key  [字段名]
	 * @param [mixed] $value [字段值]
	 * @param [int] $expire [过期时间,秒]
	 * @param [bool] 
	 */
	public function set($key = '', $value='', $expire = 3600*3)
    {
        if(!empty($key)){
            $redis = self::$redis;
            //如果是int类型的数字就不要序列化，否则用自增自减功能会失败
            $value = is_int($value) ? $value : serialize($value); 
            $redis->set($key,$value);
            if(!empty($expire)){
            	$redis->expire($key,$expire);
            }
            return true;
        }
        return false;
    }

    /**
     * [get 获取缓存字段值]
     * @param  [string] $key [键名]
     * @return [mixed]      [键值]
     */
    public function get($key)
    {
        return unserialize(self::$redis->get($key));
    }

    /**
     * 判断键名是否存在,存在返回true
     * @return bool
     */
    public function has($key='')
    {
    	return self::$redis->exists($key);
    }

    /**
     * 键值自增，如果不存在key则新增赋值为0再自增
     * @param  string $key 键名
     * @param  int $num 自增量，默认自增 1
     */
    public function inc($key='',$num=null)
    {
    	if($num==null){
    		return (int)self::$redis->incr($key);
    	}else{
    		return (int)self::$redis->incrby($key,(int)$num);
    	}
    }

    /**
     * 键值自减，如果不存在key则新增赋值为0再自减
     * @param  string $key 键名
     * @param  int $num 自减量，默认自减 1
     */
    public function dec($key='',$num=null)
    {
    	if($num==null){
    		return (int)self::$redis->decr($key);
    	}else{
    		return (int)self::$redis->decrby($key,(int)$num);
    	}
    }

    /**
     * 设置Hash表结构缓存,如果字段名为空并且字段值为一个数组则直接保存该数组
     * @param  string $key    键名
     * @param  string $field  字段名
     * @param  string $value  字段值
     * @param  int $expire 过期时间
     * @return bool
     */
    public function hset($key='',$field='',$value='',$expire=3600*3)
    {
    	if(!$key){
    		return false;
    	}
    	if(!$field && is_array($value)){
    		self::$redis->hmset($key,$value);
    	}else{
    		self::$redis->hset($key,$field,$value);
    	}
    	if(!empty($expire)){
			self::$redis->expire($key,$expire);
    	}
    	return true;
    }

    /**
     * 查询HASH表key字段键值对，如不指定字段则返回所有,如key不存在则返回空
     * @param  string $key   [description]
     * @param  string $field [description]
     * @return [type]        [description]
     */
    public function hget($key='',$field=null)
    {
    	if(!$key){
    		return false;
    	}
    	if($field==null){
    		return self::$redis->hgetall($key);
    	}
    	return self::$redis->hget($key,$field);
    }

    /**
     * 查询Hash表key中所有字段名，如key不存在则返回空
     * @param  string $key [description]
     * @return [type]      [description]
     */
    public function hkeys($key='')
    {
    	return self::$redis->hkeys($key);
    }

    /**
     * 查询Hash表key中所有字段值，如key不存在则返回空
     * @param  string $key [description]
     * @return [type]      [description]
     */
    public function hvals($key='')
    {
    	return self::$redis->hkeys($key);
    }

    /**
     * 删除指定key的字段，不存在则忽略
     * @param  string $key   键名
     * @param  string $field 字段
     * @return int       
     */
    public function hdel($key='',$field='')
    {
    	return self::$redis->hdel($key,$field);
    }

    /**
     * 删除已创建的key,支持数组批量删除
     * @param  string $key 键名
     * @return int     删除个数
     */
    public function del($key='')
    {
    	return self::$redis->del($key);
    }


}
