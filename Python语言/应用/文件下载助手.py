#-*- coding: utf-8 -*-
####################
# 文件下载助手，在命令行输入下载的连接地址
# 可查看到文件大小和下载的进度
# 最后下载的文件将会保存到同级目录下，或者你可以修改filename保存到其它目录
####################
import requests
from contextlib import closing

class ProgressBar:
	def __init__(
		self, 
		title, 
		count=0.0, 
		run_status=None, 
		fin_status=None, 
		total=100.0, 
		unit='', 
		sep='/',
		chunk_size=1.0):
		self.info = '[{title}] {status} {process} {unit} {sep} {total} {unit}'
		self.title = title
		self.total = total
		self.count = count
		self.chunk_size = chunk_size
		self.status = run_status or ''
		self.fin_status = fin_status or ' ' * len(self.status)
		self.unit = unit
		self.sep = sep
		
	def __get_info(self):
		#[名称] 状态 进度 单位 分割线 总数 单位
		return self.info.format(
		title=self.title,
		status=self.status,
		process=round(self.count / self.chunk_size, 2),
		unit=self.unit,
		sep=self.sep,
		total=round(self.total / self.chunk_size, 2)
		)
		
	def refresh(self, count=1, status=None):
		self.count += count
		self.status = status or self.status
		end_str = "\r"
		if self.count >= self.total:
			end_str = "\n"
			self.status = status or self.fin_status
		print(self.__get_info(), end=end_str,)
		
if __name__ == '__main__':
	print('#' * 50)
	print('\t欢迎使用文件下载助手')
	print('#' * 50)
	url = input('请输入需要下载的文件连接: \n')
	filename = url.split('/')[-1]
	with closing(requests.get(url, stream=True)) as response:
		chunk_size = 1024
		content_size = int(response.headers['content-length'])
		if response.status_code == 200:
			print('文件大小：{:.2f} KB'.format(content_size / chunk_size))
			progress = ProgressBar('{}下载进度'.format(filename),
				total = content_size,
				unit = 'KB',
				chunk_size = chunk_size,
				run_status = '正在下载',
				fin_status = '下载完成')
			with open(filename, 'wb') as file:
				for data in response.iter_content(chunk_size=chunk_size):
					file.write(data)
					progress.refresh(count=len(data))
		
		else:
			print('连接异常')
		
		
