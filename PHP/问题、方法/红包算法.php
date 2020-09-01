#红包算法 (可设置最大金额和最小金额) 金额单位：分
function getRedPackage($money, $num, $min, $max)
{
    $data = [];
    if ($min * $num > $money) {
        return false;
    }
    if($max*$num < $money){
        return false;
    }
    while ($num >= 1) {
        $num--;
        $kmix = max($min, $money - $num * $max);
        $kmax = min($max, $money - $num * $min);
        $kAvg = $money / ($num + 1);
        //获取最大值和最小值的距离之间的最小值
        $kDis = min($kAvg - $kmix, $kmax - $kAvg);
        //获取0到1之间的随机数与距离最小值相乘得出浮动区间，这使得浮动区间不会超出范围
        $r = ((float)(rand(1, 10000) / 10000) - 0.5) * $kDis * 2;
        $k = round($kAvg + $r);
        $money -= $k;
        $data[] = $k;
    }
    return $data;
}

$count = 100;
$allData = [];
while ($count > 0) {
  $count--;
  $data = getRedPackage(5000, 6, 10, 2000);
  echo 'money: ' . array_sum($data) . '; min: ' . min($data) . '; max: ' . max($data) . '<br />';
  $allData[] = $data;
}

print_r($allData);
