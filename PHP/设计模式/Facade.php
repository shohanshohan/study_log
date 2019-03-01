<?php
//+--------门面模式--------+
//+------一个门面旨在通过嵌入许多（但有时只有一个）接口来分离客户端和子系统，也是为了降低复杂度-------+
//+------门面不会禁止你访问子系统---------+
//+------你可以有多个门面对应一个子系统--------+
//+------好的门面是没有 new 的，并且其构造函数带有接口类型提示的参数-------+
//+------一句话理解：就是提供一个统一的接口去访问多个不同的接口--------+

//下面这个例子：跟团旅行（路线，车票，门票，住宿都一起给你搞定了）

/**
 * 子系统类共3个
 */
//风景区门票
class ScenicArea
{
  public function ticket1()
  {
    echo '游览景区1<br/>';
  }

  public function ticket2()
  {
    echo '游览景区2<br/>';
  }

  public function ticket3()
  {
    echo '游览景区3<br/>';
  }
}

//交通
class Traffic
{
  public function __construct()
  {

  }

  public function buyBusTicket($from, $to)
  {
    echo '买入从' . $from . '到' . $to . '的汽车票<br/>';
  }

  public function buyTrainTicket($from, $to)
  {
    echo '买入从' . $from . '到' . $to . '的火车票<br/>';
  }
}

//住宿
class Hotel
{
  public function book($place)
  {
    echo '预定' . $place . '的旅店<br/>';
  }

  public function rest($place)
  {
    echo '在' .$place. '预定的旅馆下榻<br/>';
  }
}



/**
 * 门面做的事就是把以上的事情集中一封装到一起了
 * 所以旅客只到这家旅行社门面就可以让其安排旅行了，而不用去自己每件事操心
 */
class Facade
{
  private $traffic;
  private $scenicArea;
  private $hotel;

  public function __construct()
  {
    $this->scenicArea = new ScenicArea();
    $this->traffic = new Traffic();
    $this->hotel = new Hotel();
  }

  public function oneDay()
  {
    $this->traffic->buyBusTicket('A', 'B');
    $this->scenicArea->ticket1();
    $this->hotel->book('C');
    $this->traffic->buyBusTicket('B', 'C');
    $this->hotel->rest('C');
    $this->scenicArea->ticket2('C');
    $this->traffic->buyTrainTicket('C', 'A');
  }

  public function twoDay()
  {
    //.........
  }


  public function threeDay()
  {
    //.........
  }
}

$agency = new Facade();
$agency->oneDay();
//一日游的流程是这样的，如下：
/*
买入从A到B的汽车票
游览景区1
预定C的旅店
买入从B到C的汽车票
在C预定的旅馆下榻
游览景区2
买入从C到A的火车票
*/
