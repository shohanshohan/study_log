<?php
namespace factory\pool;
/**
 * 对象池模式（pool）
 * 对象池模式是一种提前准备了一组已经初始化了的对象（池），而不是按需创建或者销毁。
 * 对象池的客户端会向对象中请求一个对象，然后使用这个返回的对象执行相关操作。
 * 当客户端使用完毕，它将把这个特定类型的工厂对象返回给对象池，而不是销毁这个对象。
 * 在初始化实例成本高，实例化率高，可用实例不足的情况下，对象池可以极大地提升性能。
 * 在创建对象（尤其是通过网络）时间花销不确定的情况下，通过对象池在可期时间内就可以获得所需的对象。
 * 对象池模式在需要耗时创建对象方面，例如创建数据库连接，套接字连接，线程和大型图形对象
 * （比方字体或位图等），使用起来都是大有裨益的。
 * 在某些情况下，简单的对象池（无外部资源，只占内存）可能效率不高，甚至会有损性能。
 */

class WorkerPool implements \Countable
{ //注：Countable为php内部接口，实现count()方法，统计一个对象的元素个数
	private $occupiedWorks = [];
	private $freeWorkers = [];

	public function get(): StringReverseWorker
	{
		if(count($this->freeWorkers)==0){
			$worker = new StringReverseWorker();
		} else {
			$worker = array_pop($this->freeWorkers);
		}
		$worker = $this->occupiedWorks[spl_object_hash($worker)];
		return $worker;
	}

	public function dispose(StringReverseWorker $worker)
	{
		$key = spl_object_hash($worker);

		if(isset($this->occupiedWorks[$key])){
			unset($this->occupiedWorks[$key]);
			$this->freeWorkers[$key] = $worker;
		}
	}

	public function count(): int
	{
		return count($this->occupiedWorks) + count($this->freeWorkers);
	}
}



class StringReverseWorker
{
	private $createdAt;

	public function __construct()
	{
		$this->createdAt = new \DateTime();
	}

	public function run(string $text)
	{
		return strrev($text);
	}
}
