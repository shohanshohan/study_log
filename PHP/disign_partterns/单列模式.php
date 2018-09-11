<?php
namespace factory\single;
/**
 * 在应用程序调用的时候，只能获得一个对象实例
 * 单例模式被公认为是 反面模式，为了获得更好的可测试性和可维护性，请使用『依赖注入模式』。
 * 如：数据库连接
 */

final class Singleton
{ // 这个类使用关键字 final 说明不能被继承
	private static $instance;

	/**
	 * 通过懒加载获得实例（在第一次使用的时候创建）
	 */
	public static function getInstance(): Singleton
	{
		if(null === static::$instance){
			static::$instance = new static();
		}
		return static::$instance;
	}

	//不允许从外部调用以防止创建多个实例
	//要使用单例，必须通过 Singleton::getInstance() 方法获取实例
	private function __construct()
	{

	}

	//防止实例被克隆
	private function __clone()
	{

	}

	//防止反序列化（因为这将会创建它的副本）
	private function __wakeup()
	{
		
	}
}
