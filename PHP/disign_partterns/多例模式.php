<?php
namespace factory\multition;
/**
 * 多例模式是指存在一个类有多个相同的实例，而且该实例都是该类本身。这个类叫做多例类
 * 多例模式是单例模式的推广
 * 多例模式被公认为是 反面模式，为了获得更好的可测试性和可维护性，请使用『依赖注入模式』
 */
final class Multition
{
	const INSTANCE_1 = '1';
	const INSTANCE_2 = '2';

	//实例数组
	private static $instances = [];

	public static function getInstance(string $instanceName): Multition
	{
		if(!isset(self::$instances[$instanceName])){
			self::$instances[$instanceName] = new self();
		}
		return self::$instances[$instanceName];
	} 

	private function __construct()
	{

	}

	private function __wakeup()
	{

	}

	private function __clone()
	{
		
	}
}
