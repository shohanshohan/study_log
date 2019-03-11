/**
# ===================================================================
# 约瑟夫环（约瑟夫问题）是一个数学的应用问题：
# 已知n个人（以编号1，2，3...n分别表示）围坐在一张圆桌周围。
# 从编号为k的人开始报数，数到m的那个人出列；他的下一个人又从1开始报数，数到m的那个人又出列；
# 依此规律重复下去，直到圆桌周围的人全部出列或规定留下的人数
# ====================================================================
**/


function yuesefu($nums, $step, $stay)
{
  $check = 0;
  $list = range(1, $nums+1);
  while (count($list) > $stay) {
    foreach ($list as $key => $value) {
      $check += 1;
      if($check == $step){
        $check = 0;
        echo $value . "号被移除<br />";
        unset($list[$key]);
      }
    }
  }
  return $list;
}

$stays = yuesefu(30, 9, 15);
echo '最后留下的人编号：' . implode(',', $stays);
