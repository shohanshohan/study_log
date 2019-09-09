import requests
import re
import json

def request_dangdang(url):
	try:
		response = requests.get(url)
		if response.status_code == 200:
			return response.text
	except requests.RequestException:
		return None


def parse_result(html):
	pattern = re.compile((
		'<li>.*?list_num.*?(\d+).</div>.*?'
		'<img src="(.*?)".*?class="name".*?title="(.*?)">.*?'
		'class="star">.*?<a.*?>(\d+)条评论</a>.*?'
		'class="tuijian">(.*?)</span>.*?'
		'class="publisher_info">.*?title="(.*?)".*?'
		'class="biaosheng">.*?<span>(.*?)</span></div>.*?'
		'<p><span\sclass="price_n">&yen;(.*?)</span>.*?</li>'
	), re.S)
	items = re.findall(pattern, html)
	for item in items:
		yield {
			'range': item[0],
			'image': item[1],
			'title': item[2],
			'comment': str(item[3]) + '条评论',
			'recommend': item[4],
			'author': item[5],
			'star_times': '标星' + str(item[6]) + '次',
			'price': '价格' + str(item[7]) + '元'
		}

def write_item_to_file(items):
	with open('book.txt', 'a', encoding='utf-8') as f:
		print('开始写入数据 ======》')
		for item in items:
			f.write(json.dumps(item, ensure_ascii=False) + '\n')
		
		
def main(page):
	url = 'http://bang.dangdang.com/books/fivestars/01.00.00.00.00.00-recent30-0-0-1-' + str(page)
	html = request_dangdang(url)
	items = parse_result(html)
	print('开始分析数据 《======》')
	write_item_to_file(items)
	
		
if __name__ == '__main__':
	main(1)
	#for i in range(1, 11):
		#main(i)
	#str = '<li>我就是不要括号啦！（讨厌的括号内容太长了）</li>'
	#res = re.search('<li>(.*?(?=（)).*?</li>', str)
	#print(res[1])
	print('写入数据已完成')
