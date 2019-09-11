import requests
import re
import json
from bs4 import BeautifulSoup
import xlwt

movie_url = 'https://movie.douban.com/top250'

def request_page(url):
	try:
		response = requests.get(url)
		if response.status_code == 200:
			return response.text
	except requests.RequestException:
		return None

def movie_info(movie_url, page):
	movie_url += '?start=' + str(page * 25)
	html = request_page(movie_url)
	soup = BeautifulSoup(html, 'lxml')
	ol = soup.find('ol', 'grid_view')
	for item in ol.find_all('li'):
		yield {
			'ranking': item.em.string, #排名
			'name': item.find('span', 'title').string, #名称
			'image': item.img['src'], #图片地址
			'score': item.find('span', 'rating_num').string, #评分
			'author': item.find('p').contents[0].strip(), #导演（主演）
			'describe': item.find('p').contents[2].strip(), #说明
			'intro': item.find('span', 'inq').string if item.find('span', 'inq') else '' #导言,这里要防止有些空的
		}
		
def write_item_to_txt(items):
	with open('movie.txt', 'a', encoding='utf-8') as f:
		print('写入数据...')
		for item in items:
			f.write(json.dumps(item, ensure_ascii=False) + '\n')

def write_item_to_xlsx(sheet, items, page):
	i = page * 25
	for item in items:
		print('写入数据--'+item['ranking']+'...')
		i += 1
		sheet.write(i, 0, item['ranking'])
		sheet.write(i, 1, item['name'])
		sheet.write(i, 2, item['image'])
		sheet.write(i, 3, item['score'])
		sheet.write(i, 4, item['author'])
		sheet.write(i, 5, item['describe'])
		sheet.write(i, 6, item['intro'])
	

def main():
	book = xlwt.Workbook(encoding='utf-8', style_compression=0)
	sheet = book.add_sheet('豆瓣电影Top250', cell_overwrite_ok=True)
	sheet.write(0,0,'排名')
	sheet.write(0,1,'名称')
	sheet.write(0,2,'图片地址')
	sheet.write(0,3,'评分')
	sheet.write(0,4,'导演（主演）')
	sheet.write(0,5,'说明')
	sheet.write(0,6,'导言')
	for page in range(25):
		items = movie_info(movie_url, page)
		#write_item_to_txt(items)
		write_item_to_xlsx(sheet, items, page)
	
	book.save(u'豆瓣最受欢迎的250部电影.xlsx')
	print('数据写入完成！')
		
		
if __name__ == '__main__':
	main()


