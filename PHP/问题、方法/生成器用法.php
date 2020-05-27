<?php
// 使用 yield 关键字，在需要处理大量数据时会很有用
// 因为并不需要集中获取，而是分次获取，也就是用到它的时候才执行运算，这样可以节省资源开销
// 看下面的例子

//常规方法，生成随机数据
function createRange($num) {
  $data = [];
  for ($i=0; $i<$num; $i++) {
    //yield time();
    $data[] = time();
  }
  return $data;
}

//利用 yield 封装
function createYieldRange($num) {
  for ($i=0; $i<$num; $i++) {
    yield time();
  }
}

$result = createRange(10);
foreach ($result as $v) {
  sleep(1);
  echo $v.'<br />';  //这里打印出来的时间截都是一样的，因为 $result 的结果是一次集中打包出来的
}

$result2 = createYieldRange(10);
foreach ($result2 as $v2) {
  sleep(1);
  echo $v2.'<br />';  //这里打印出来的时间截间隔一秒，因为 $result2 的结果是分次生成的
}


