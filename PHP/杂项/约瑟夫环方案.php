<?php

# ===================================================================
# 约瑟夫环（约瑟夫问题）是一个数学的应用问题：
# 已知n个人（以编号1，2，3...n分别表示）围坐在一张圆桌周围。
# 从编号为k的人开始报数，数到m的那个人出列；他的下一个人又从1开始报数，数到m的那个人又出列；
# 依此规律重复下去，直到圆桌周围的人全部出列或规定留下的人数
# ====================================================================



#参数 $nums：人数，$step: 数到几的步数，$stay: 最后留下多少人
function yuesefu($nums, $step, $stay)
{
  $list = range(1, $nums);
  $check = 0;
  while (count($list) > $stay) {
    foreach ($list as $key => $value) {
      $check += 1;
      if($check == $step) {
        $check = 0;
        echo $value . " 号出列了<br>";
        unset($list[$key]);
      }
    }
  }
  return $list;
}

$stay = yuesefu(30,5,2);
echo '最后留下的号数：[' . implode(',', $stay) . ']';
