<?php
$mid = $_POST['mid'];
if ($mid) {
	echo json_encode(array('code'=>'200','msg'=>'ajax传输成功！已拿到数据mid='.$mid));
}