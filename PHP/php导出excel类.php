<?php

/**
* 数据转换成excel表导出类
*/
class ExcelExport
{
	private $title = [];
	private $filename = '';
	private $filter = []; 

	/*
	* 导出excel数据表
	* @param array $data 要导出的数据
	*/
	public function excel($data=[])
	{
	    header("Content-type:application/vnd.ms-excel");
	    header("Content-Disposition:attachment;filename=" . date('Y-m-d') . $this->filename . ".xls");
		header("Content-type: application/octet-stream;charset=utf-8");

		if($data && is_array($data)){
			$filter = $this->filter;
			if(isset($data[0]) && is_array($data[0])){
				$keys = array_keys($data[0]);
				foreach ($data as &$value) {
					foreach ($value as $k => &$v) {
						if(isset($filter[$k])){
							if($filter[$k]=='datetime'){
								$v = date("Y-m-d H:i:s",$v);
							}
							if($filter[$k]=='date'){
								$v = date("Y-m-d",$v);
							}
							if(is_array($filter[$k])){
								$v = isset($filter[$k][$v]) ? $filter[$k][$v] : $v;
							}
						}
					}
					$value = implode("\t", $value);
				}
				$excel = implode("\n", $data);
			}else{
				$keys = array_keys($data);
				foreach ($data as $key => &$value) {
					$value = isset($filter[$key][$value]) ? $filter[$key][$value] : $value;
				}
				$excel = implode("\t", $data);
			}
			$title = $this->titleColumn($keys);
			echo implode("\t", $title) . "\n";
			echo $excel;	
			exit();
		}else{
			$this->show_error('暂无数据！');
		}
	}

	/**
	 * 设置标题
	 * @param array $title 标题参数为字段名对应标题名称的键值对数组
	 * @return obj this 
	 */
	public function title($title)
	{
		if($title && is_array($title)){
			$this->title = $title;
		}
		return $this;
	}


	/**
	 * 设置导出的文件名
	 * @param string $filename 文件名
	 * @return obj this 
	 */
	public function filename($filename)
	{
		$this->filename = date('Y-m-d') . (string)$filename;
		return $this;
	}


	/**
	 * 设置字段过滤器
	 * @param array $filter 文件名
	 * @return obj this 
	 */
	public function filter($filter)
	{
		$this->filter = (array)$filter;
		return $this;
	}

	/**
	 * 确保标题字段名和数据字段名一致
	 * @param  array $keys  要显示的字段名数组
	 * @return array 包含所有要显示的字段名的标题数组
	 */
	protected function titleColumn($keys)
	{
		$title = $this->title;
		if($title && is_array($title)){
			$titleData = [];
			foreach ($keys as $v) {
				$titleData[$v] = isset($title[$v]) ? $title[$v] : $v;
			}
			return $titleData;
		}
		return $keys;
	}

	/**
	 * 错误信息提示框
	 * @param  string $msg 错误提示信息
	 * @return html
	 */
	public function show_error($error)
	{
		$showEvent = "<script> var showerror = document.getElementById('show-error'); function showEvent(){setTimeout(function(){showerror.style.display='none';}, 3000);} document.getElementById('close-error-btn').onclick=function(){showerror.style.display='none';}; showerror.onmouseover=showEvent(); showerror.onmouseout=showEvent();</script>";
		$html = '<h2 id="show-error" style="width:45%;padding:10px 0px 20px 0px;border:1px solid #ccc;box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19)!important;position:absolute;top:100px;left:50%;margin-left:-23%;text-align:center;color:red;"><span id="close-error-btn" style="display:block;width: 100%;cursor:pointer;margin-top:-10px;text-align:right;" >X&nbsp;</span>'.(string)$error.'</h2>';
		echo $html . $showEvent;
		exit();
	}
	
}


$obj = new ExcelExport();
$data = [['name'=>'piter','age'=>'28','sex'=>1,'create_time'=>time()],['name'=>'lily','age'=>'18','sex'=>0,'create_time'=>time()]];
$title = ['name'=>'姓名','age'=>'年龄','sex'=>'性别','create_time'=>'日期'];
$filename = 'test';
$filter = ['sex'=>[1=>'男',0=>'女'],'create_time'=>'datetime'];
$obj->title($title)->filename($filename)->filter($filter)->excel($data);
