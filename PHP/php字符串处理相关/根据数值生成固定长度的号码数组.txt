function fixedArr($num, $prefix)
{
  $length = strlen($num);
  $arr = [];
  for ($i=1; $i <= $num ; $i++) { 
    $pad = str_pad($i, $length, '0', STR_PAD_LEFT); //不够数的填充0在左边
    $str = $prefix . $pad;
    array_push($arr, (int)$str);
  }
  return $arr;
}
