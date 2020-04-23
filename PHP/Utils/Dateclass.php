<?php
/**
* 获取日期时间操作类
*/
class Dateclass
{
  /**
   * [getdateData 分页获取日期数组（按查询日期区间和分页）]
   * @param  [type]  $startDate [开始日期]
   * @param  [type]  $endDate   [结束日期]
   * @param  integer $offset    [分页起始点]
   * @param  integer $limit     [查询条数]
   * @param  string  $timezone  [时区]
   * @param  string  $orderBy   [排序,升序asc,默认为倒序desc]
   * @param  [type]  $interval  [日期间隔/天,默认1天]
   * @return [type]             [Array]
   */
  public static function getDates($startDate, $endDate, $offset=0, $limit=15, $timezone='Asia/Shanghai', $orderBy='desc', $interval=1)
  {
    date_default_timezone_set($timezone);
    if($orderBy === 'desc') { 
      //降序
      $firstDate = $offset==0 ? $endDate : date('Y-m-d', strtotime($endDate) - 86400*$offset);
      $intervalStr = (0-$interval).' day';
    } else { 
      //升序
      $firstDate = $offset==0 ? $startDate : date('Y-m-d', strtotime($startDate) + 86400*$offset);;
      $intervalStr = $interval.' day';
    }
    $datetime = new DateTime($firstDate); //使用这个类时记得要设置时区，否则会报错
    $dateInterval = DateInterval::createFromDateString($intervalStr);
    $period = new DatePeriod($datetime, $dateInterval, $limit-1); //从0开始记，所以条数这里减去1
    $dates = [];
    foreach ($period as $date) {
      $dateStr = $date->format('Y-m-d');
      if( ($orderBy=='desc' && $dateStr<$startDate) || ($orderBy!='desc' && $dateStr>$endDate) ) {
        break; //如果超出日期范围，则退出循环
      }
      $dates[] = $dateStr;
    }
    return $dates;
  }
  
}
