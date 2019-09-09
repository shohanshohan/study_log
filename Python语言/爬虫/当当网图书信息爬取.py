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
	

'''
#上面的代码用了re模块的正则来选出我们想要的内容，当我们选择的内容很多的时候，而且文档结构也有规律性
#这时候我们可以使用 BeautifulSoup4模块来更快地帮助我们选择想要的内容
我们打开页面http://bang.dangdang.com/books/fivestars/01.00.00.00.00.00-recent30-0-0-1-1
可以找到一些规律
我们要选取的内容全部在下面的结构文档中
<ul class="bang_list clearfix bang_list_mode">
    <li class="">
    <div class="list_num red">1.</div>   
    <div class="pic"><a href="http://product.dangdang.com/25345988.html" target="_blank"><img src="http://img3m8.ddimg.cn/8/26/25345988-1_l_1.jpg" alt="有话说出来！（彻底颠覆社会人脉的固有方式，社交电池帮你搞定社交。社交恐惧症患者必须拥有的一本实用社交指南，初入大学和职场的必备“攻略”，拿起这本书，你也是“魏璎珞”）纤阅出品" title="有话说出来！（彻底颠覆社会人脉的固有方式，社交电池帮你搞定社交。社交恐惧症患者必须拥有的一本实用社交指南，初入大学和职场的必备“攻略”，拿起这本书，你也是“魏璎珞”）纤阅出品"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/25345988.html" target="_blank" title="有话说出来！（彻底颠覆社会人脉的固有方式，社交电池帮你搞定社交。社交恐惧症患者必须拥有的一本实用社交指南，初入大学和职场的必备“攻略”，拿起这本书，你也是“魏璎珞”）纤阅出品">有话说出来！（彻底颠覆社会人脉的固有方式，社交电池帮你搞定社<span class="dot">...</span></a></div>    
    <div class="star"><span class="level"><span style="width: 100%;"></span></span><a href="http://product.dangdang.com/25345988.html?point=comment_point" target="_blank">17773条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info">【美】<a href="http://search.dangdang.com/?key=帕特里克·金" title="【美】帕特里克·金 著，张捷/李旭阳 译" target="_blank">帕特里克·金</a> 著，<a href="http://search.dangdang.com/?key=张捷" title="【美】帕特里克·金 著，张捷/李旭阳 译" target="_blank">张捷</a>/<a href="http://search.dangdang.com/?key=李旭阳" title="【美】帕特里克·金 著，张捷/李旭阳 译" target="_blank">李旭阳</a> 译</div>    
    <div class="publisher_info"><span>2018-08-01</span>&nbsp;<a href="http://search.dangdang.com/?key=天津人民出版社" target="_blank">天津人民出版社</a></div>    

            <div class="biaosheng">五星评分：<span>16274次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥21.00</span>
                        <span class="price_r">¥42.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('25345988');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_25345988" name="" href="javascript:showMsgBox('addto_favorlist_25345988',encodeURIComponent('25345988&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li class="">
    <div class="list_num red">2.</div>   
    <div class="pic"><a href="http://product.dangdang.com/25254451.html" target="_blank"><img src="http://img3m1.ddimg.cn/46/27/25254451-1_l_5.jpg" alt="《另类间谍》（推理小说名家赫尔曼之战争三部曲 展现人性深处的生死博弈）" title="《另类间谍》（推理小说名家赫尔曼之战争三部曲 展现人性深处的生死博弈）"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/25254451.html" target="_blank" title="《另类间谍》（推理小说名家赫尔曼之战争三部曲 展现人性深处的生死博弈）">《另类间谍》（推理小说名家赫尔曼之战争三部曲 展现人性深处的生<span class="dot">...</span></a></div>    
    <div class="star"><span class="level"><span style="width: 97.6%;"></span></span><a href="http://product.dangdang.com/25254451.html?point=comment_point" target="_blank">10815条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info">[美] <a href="http://search.dangdang.com/?key=莉比·菲舍尔·赫尔曼" title="[美] 莉比·菲舍尔·赫尔曼 著 ,汪德均 译" target="_blank">莉比·菲舍尔·赫尔曼</a> 著 ,<a href="http://search.dangdang.com/?key=汪德均" title="[美] 莉比·菲舍尔·赫尔曼 著 ,汪德均 译" target="_blank">汪德均</a> 译</div>    
    <div class="publisher_info"><span>2017-03-01</span>&nbsp;<a href="http://search.dangdang.com/?key=天津人民出版社" target="_blank">天津人民出版社</a></div>    

            <div class="biaosheng">五星评分：<span>9698次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥14.50</span>
                        <span class="price_r">¥29.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('25254451');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_25254451" name="" href="javascript:showMsgBox('addto_favorlist_25254451',encodeURIComponent('25254451&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li class="">
    <div class="list_num red">3.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27911609.html" target="_blank"><img src="http://img3m9.ddimg.cn/44/30/27911609-1_l_8.jpg" alt="春日序曲（《请以你的名字呼唤我》第90届奥斯卡获奖影片原著作者全新洒泪力作）" title="春日序曲（《请以你的名字呼唤我》第90届奥斯卡获奖影片原著作者全新洒泪力作）"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27911609.html" target="_blank" title="春日序曲（《请以你的名字呼唤我》第90届奥斯卡获奖影片原著作者全新洒泪力作）">春日序曲（《请以你的名字呼唤我》第90届奥斯卡获奖影片原著作者<span class="dot">...</span></a></div>    
    <div class="star"><span class="level"><span style="width: 99.6%;"></span></span><a href="http://product.dangdang.com/27911609.html?point=comment_point" target="_blank">4258条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=安德烈·艾席蒙" title="安德烈·艾席蒙  白马时光 出品" target="_blank">安德烈·艾席蒙</a>  <a href="http://search.dangdang.com/?key=白马时光" title="安德烈·艾席蒙  白马时光 出品" target="_blank">白马时光</a> 出品</div>    
    <div class="publisher_info"><span>2019-08-01</span>&nbsp;<a href="http://search.dangdang.com/?key=百花洲文艺出版社" target="_blank">百花洲文艺出版社</a></div>    

            <div class="biaosheng">五星评分：<span>3988次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥22.50</span>
                        <span class="price_r">¥45.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27911609');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_27911609" name="" href="javascript:showMsgBox('addto_favorlist_27911609',encodeURIComponent('27911609&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">4.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27888802.html" target="_blank"><img src="http://img3m2.ddimg.cn/7/15/27888802-1_l_2.jpg" alt="人生需要高级感" title="人生需要高级感"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27888802.html" target="_blank" title="人生需要高级感">人生需要高级感</a></div>    
    <div class="star"><span class="level"><span style="width: 99.8%;"></span></span><a href="http://product.dangdang.com/27888802.html?point=comment_point" target="_blank">3609条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=西风南浦" title="西风南浦 著,  悦读纪 出品" target="_blank">西风南浦</a> 著,  <a href="http://search.dangdang.com/?key=悦读纪" title="西风南浦 著,  悦读纪 出品" target="_blank">悦读纪</a> 出品</div>    
    <div class="publisher_info"><span>2019-07-01</span>&nbsp;<a href="http://search.dangdang.com/?key=青岛出版社" target="_blank">青岛出版社</a></div>    

            <div class="biaosheng">五星评分：<span>3246次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥19.90</span>
                        <span class="price_r">¥39.80</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e">电子书：<span class="price_n">¥19.00</span></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27888802');" class="listbtn_buy">加入购物车</a>
                        
                        <a name="" href="http://product.dangdang.com/1901136305.html" class="listbtn_buydz" target="_blank">购买电子书</a>
                        <a ddname="加入收藏" id="addto_favorlist_27888802" name="" href="javascript:showMsgBox('addto_favorlist_27888802',encodeURIComponent('27888802&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li class="">
    <div class="list_num ">5.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27889474.html" target="_blank"><img src="http://img3m4.ddimg.cn/85/21/27889474-1_l_2.jpg" alt="养成良好习惯，高效管理时间" title="养成良好习惯，高效管理时间"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27889474.html" target="_blank" title="养成良好习惯，高效管理时间">养成良好习惯，高效管理时间</a></div>    
    <div class="star"><span class="level"><span style="width: 93.8%;"></span></span><a href="http://product.dangdang.com/27889474.html?point=comment_point" target="_blank">14956条评论</a><span class="tuijian">99.9%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=杰米·希尔" title="杰米·希尔著， 刘阳晓露/关芊蔚/安捷 译" target="_blank">杰米·希尔</a>著， <a href="http://search.dangdang.com/?key=刘阳晓露" title="杰米·希尔著， 刘阳晓露/关芊蔚/安捷 译" target="_blank">刘阳晓露</a>/<a href="http://search.dangdang.com/?key=关芊蔚" title="杰米·希尔著， 刘阳晓露/关芊蔚/安捷 译" target="_blank">关芊蔚</a>/<a href="http://search.dangdang.com/?key=安捷" title="杰米·希尔著， 刘阳晓露/关芊蔚/安捷 译" target="_blank">安捷</a> 译</div>    
    <div class="publisher_info"><span>2019-04-30</span>&nbsp;<a href="http://search.dangdang.com/?key=天津人民出版社" target="_blank">天津人民出版社</a></div>    

            <div class="biaosheng">五星评分：<span>12331次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥17.50</span>
                        <span class="price_r">¥35.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27889474');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_27889474" name="" href="javascript:showMsgBox('addto_favorlist_27889474',encodeURIComponent('27889474&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">6.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27915454.html" target="_blank"><img src="http://img3m4.ddimg.cn/28/27/27915454-1_l_3.jpg" alt="凯叔·神奇图书馆 海洋X计划：海中霸主来袭（中国版神奇校车，专为儿童打造的科幻小说，让孩子读故事，学科学，探索海洋世界）" title="凯叔·神奇图书馆 海洋X计划：海中霸主来袭（中国版神奇校车，专为儿童打造的科幻小说，让孩子读故事，学科学，探索海洋世界）"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27915454.html" target="_blank" title="凯叔·神奇图书馆 海洋X计划：海中霸主来袭（中国版神奇校车，专为儿童打造的科幻小说，让孩子读故事，学科学，探索海洋世界）">凯叔·神奇图书馆 海洋X计划：海中霸主来袭（中国版神奇校车，专<span class="dot">...</span></a></div>    
    <div class="star"><span class="level"><span style="width: 0%;"></span></span><a href="http://product.dangdang.com/27915454.html?point=comment_point" target="_blank">2032条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=凯叔" title="凯叔，果麦文化 出品" target="_blank">凯叔</a>，<a href="http://search.dangdang.com/?key=果麦文化" title="凯叔，果麦文化 出品" target="_blank">果麦文化</a> 出品</div>    
    <div class="publisher_info"><span>2019-08-01</span>&nbsp;<a href="http://search.dangdang.com/?key=云南美术出版社" target="_blank">云南美术出版社</a></div>    

            <div class="biaosheng">五星评分：<span>2022次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥24.50</span>
                        <span class="price_r">¥49.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27915454');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_27915454" name="" href="javascript:showMsgBox('addto_favorlist_27915454',encodeURIComponent('27915454&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">7.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27889447.html" target="_blank"><img src="http://img3m7.ddimg.cn/58/31/27889447-1_l_2.jpg" alt="白手起家开公司" title="白手起家开公司"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27889447.html" target="_blank" title="白手起家开公司">白手起家开公司</a></div>    
    <div class="star"><span class="level"><span style="width: 97.4%;"></span></span><a href="http://product.dangdang.com/27889447.html?point=comment_point" target="_blank">6725条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=胡华成" title="胡华成" target="_blank">胡华成</a></div>    
    <div class="publisher_info"><span>2019-07-01</span>&nbsp;<a href="http://search.dangdang.com/?key=电子工业出版社" target="_blank">电子工业出版社</a></div>    

            <div class="biaosheng">五星评分：<span>6306次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥27.50</span>
                        <span class="price_r">¥55.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e">电子书：<span class="price_n">¥38.50</span></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27889447');" class="listbtn_buy">加入购物车</a>
                        
                        <a name="" href="http://product.dangdang.com/1901135711.html" class="listbtn_buydz" target="_blank">购买电子书</a>
                        <a ddname="加入收藏" id="addto_favorlist_27889447" name="" href="javascript:showMsgBox('addto_favorlist_27889447',encodeURIComponent('27889447&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">8.</div>   
    <div class="pic"><a href="http://product.dangdang.com/25069963.html" target="_blank"><img src="http://img3m3.ddimg.cn/94/21/25069963-1_l_16.jpg" alt="呐喊——大屠杀回忆录（二战犹太大屠杀幸存者自述——一部震撼人心的自传）" title="呐喊——大屠杀回忆录（二战犹太大屠杀幸存者自述——一部震撼人心的自传）"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/25069963.html" target="_blank" title="呐喊——大屠杀回忆录（二战犹太大屠杀幸存者自述——一部震撼人心的自传）">呐喊——大屠杀回忆录（二战犹太大屠杀幸存者自述——一部震撼人<span class="dot">...</span></a></div>    
    <div class="star"><span class="level"><span style="width: 95.8%;"></span></span><a href="http://product.dangdang.com/25069963.html?point=comment_point" target="_blank">27091条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=曼尼·斯坦伯格" title="曼尼·斯坦伯格 著，张佳敏 余林杰 译" target="_blank">曼尼·斯坦伯格</a> 著，<a href="http://search.dangdang.com/?key=张佳敏" title="曼尼·斯坦伯格 著，张佳敏 余林杰 译" target="_blank">张佳敏</a> <a href="http://search.dangdang.com/?key=余林杰" title="曼尼·斯坦伯格 著，张佳敏 余林杰 译" target="_blank">余林杰</a> 译</div>    
    <div class="publisher_info"><span>2017-04-01</span>&nbsp;<a href="http://search.dangdang.com/?key=天津人民出版社" target="_blank">天津人民出版社</a></div>    

            <div class="biaosheng">五星评分：<span>20751次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥19.00</span>
                        <span class="price_r">¥38.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('25069963');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_25069963" name="" href="javascript:showMsgBox('addto_favorlist_25069963',encodeURIComponent('25069963&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">9.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27915455.html" target="_blank"><img src="http://img3m5.ddimg.cn/29/28/27915455-1_l_2.jpg" alt="凯叔·神奇图书馆 海洋X计划：南极秘境（中国版神奇校车，专为儿童打造的科幻小说，让孩子读故事，学科学，探索海洋世界）" title="凯叔·神奇图书馆 海洋X计划：南极秘境（中国版神奇校车，专为儿童打造的科幻小说，让孩子读故事，学科学，探索海洋世界）"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27915455.html" target="_blank" title="凯叔·神奇图书馆 海洋X计划：南极秘境（中国版神奇校车，专为儿童打造的科幻小说，让孩子读故事，学科学，探索海洋世界）">凯叔·神奇图书馆 海洋X计划：南极秘境（中国版神奇校车，专为儿<span class="dot">...</span></a></div>    
    <div class="star"><span class="level"><span style="width: 0%;"></span></span><a href="http://product.dangdang.com/27915455.html?point=comment_point" target="_blank">1961条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=凯叔" title="凯叔， 果麦文化 出品" target="_blank">凯叔</a>， <a href="http://search.dangdang.com/?key=果麦文化" title="凯叔， 果麦文化 出品" target="_blank">果麦文化</a> 出品</div>    
    <div class="publisher_info"><span>2019-08-01</span>&nbsp;<a href="http://search.dangdang.com/?key=云南美术出版社" target="_blank">云南美术出版社</a></div>    

            <div class="biaosheng">五星评分：<span>1953次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥24.50</span>
                        <span class="price_r">¥49.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27915455');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_27915455" name="" href="javascript:showMsgBox('addto_favorlist_27915455',encodeURIComponent('27915455&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">10.</div>   
    <div class="pic"><a href="http://product.dangdang.com/24157581.html" target="_blank"><img src="http://img3m1.ddimg.cn/96/22/24157581-1_l_15.jpg" alt="魔力四射：如何打动、亲近和影响他人（一本改变人生思维方式的魔力读物，助你掌握社交技巧与领导魅力）" title="魔力四射：如何打动、亲近和影响他人（一本改变人生思维方式的魔力读物，助你掌握社交技巧与领导魅力）"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/24157581.html" target="_blank" title="魔力四射：如何打动、亲近和影响他人（一本改变人生思维方式的魔力读物，助你掌握社交技巧与领导魅力）">魔力四射：如何打动、亲近和影响他人（一本改变人生思维方式的魔<span class="dot">...</span></a></div>    
    <div class="star"><span class="level"><span style="width: 98.8%;"></span></span><a href="http://product.dangdang.com/24157581.html?point=comment_point" target="_blank">18284条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=帕特里克·金" title="帕特里克·金 著，宗丹/田坤 译" target="_blank">帕特里克·金</a> 著，<a href="http://search.dangdang.com/?key=宗丹" title="帕特里克·金 著，宗丹/田坤 译" target="_blank">宗丹</a>/<a href="http://search.dangdang.com/?key=田坤" title="帕特里克·金 著，宗丹/田坤 译" target="_blank">田坤</a> 译</div>    
    <div class="publisher_info"><span>2016-08-01</span>&nbsp;<a href="http://search.dangdang.com/?key=天津人民出版社" target="_blank">天津人民出版社</a></div>    

            <div class="biaosheng">五星评分：<span>14954次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥16.00</span>
                        <span class="price_r">¥32.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('24157581');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_24157581" name="" href="javascript:showMsgBox('addto_favorlist_24157581',encodeURIComponent('24157581&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">11.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27882633.html" target="_blank"><img src="http://img3m3.ddimg.cn/75/25/27882633-1_l_3.jpg" alt="我们的历史 幼儿趣味中国历史绘本 套装共10册" title="我们的历史 幼儿趣味中国历史绘本 套装共10册"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27882633.html" target="_blank" title="我们的历史 幼儿趣味中国历史绘本 套装共10册">我们的历史 幼儿趣味中国历史绘本 套装共10册</a></div>    
    <div class="star"><span class="level"><span style="width: 100%;"></span></span><a href="http://product.dangdang.com/27882633.html?point=comment_point" target="_blank">3614条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=陈丽华" title="陈丽华" target="_blank">陈丽华</a></div>    
    <div class="publisher_info"><span>2019-06-01</span>&nbsp;<a href="http://search.dangdang.com/?key=明天出版社" target="_blank">明天出版社</a></div>    

            <div class="biaosheng">五星评分：<span>1582次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥116.22</span>
                        <span class="price_r">¥298.00</span>(<span class="price_s">3.9折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27882633');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_27882633" name="" href="javascript:showMsgBox('addto_favorlist_27882633',encodeURIComponent('27882633&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">12.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27887253.html" target="_blank"><img src="http://img3m3.ddimg.cn/42/20/27887253-1_l_6.jpg" alt="生活需要淡定感（《哈利·波特》作者J.K.罗琳  重点推荐）" title="生活需要淡定感（《哈利·波特》作者J.K.罗琳  重点推荐）"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27887253.html" target="_blank" title="生活需要淡定感（《哈利·波特》作者J.K.罗琳  重点推荐）">生活需要淡定感（《哈利·波特》作者J.K.罗琳  重点推荐）</a></div>    
    <div class="star"><span class="level"><span style="width: 99.4%;"></span></span><a href="http://product.dangdang.com/27887253.html?point=comment_point" target="_blank">3428条评论</a><span class="tuijian">99.9%推荐</span></div>    
    <div class="publisher_info">[英]<a href="http://search.dangdang.com/?key=威廉姆·布鲁姆" title="[英]威廉姆·布鲁姆" target="_blank">威廉姆·布鲁姆</a></div>    
    <div class="publisher_info"><span>2019-06-30</span>&nbsp;<a href="http://search.dangdang.com/?key=湖南人民出版社" target="_blank">湖南人民出版社</a></div>    

            <div class="biaosheng">五星评分：<span>2591次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥21.00</span>
                        <span class="price_r">¥42.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27887253');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_27887253" name="" href="javascript:showMsgBox('addto_favorlist_27887253',encodeURIComponent('27887253&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">13.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27915837.html" target="_blank"><img src="http://img3m7.ddimg.cn/15/3/27915837-1_l_3.jpg" alt="抖音营销138招：一本书教会你玩赚抖音" title="抖音营销138招：一本书教会你玩赚抖音"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27915837.html" target="_blank" title="抖音营销138招：一本书教会你玩赚抖音">抖音营销138招：一本书教会你玩赚抖音</a></div>    
    <div class="star"><span class="level"><span style="width: 100%;"></span></span><a href="http://product.dangdang.com/27915837.html?point=comment_point" target="_blank">1544条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=招商哥" title="招商哥   王菲彤" target="_blank">招商哥</a>   <a href="http://search.dangdang.com/?key=王菲彤" title="招商哥   王菲彤" target="_blank">王菲彤</a></div>    
    <div class="publisher_info"><span>2019-07-21</span>&nbsp;<a href="http://search.dangdang.com/?key=清华大学出版社" target="_blank">清华大学出版社</a></div>    

            <div class="biaosheng">五星评分：<span>1510次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥44.20</span>
                        <span class="price_r">¥59.00</span>(<span class="price_s">7.5折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27915837');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_27915837" name="" href="javascript:showMsgBox('addto_favorlist_27915837',encodeURIComponent('27915837&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">14.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27904794.html" target="_blank"><img src="http://img3m4.ddimg.cn/60/23/27904794-1_l_12.jpg" alt="只能陪你走一程（蕊希2019新作）" title="只能陪你走一程（蕊希2019新作）"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27904794.html" target="_blank" title="只能陪你走一程（蕊希2019新作）">只能陪你走一程（蕊希2019新作）</a></div>    
    <div class="star"><span class="level"><span style="width: 88.6%;"></span></span><a href="http://product.dangdang.com/27904794.html?point=comment_point" target="_blank">33014条评论</a><span class="tuijian">99.7%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=蕊希" title="蕊希 著,博集天卷 出品" target="_blank">蕊希</a> 著,<a href="http://search.dangdang.com/?key=博集天卷" title="蕊希 著,博集天卷 出品" target="_blank">博集天卷</a> 出品</div>    
    <div class="publisher_info"><span>2019-08-01</span>&nbsp;<a href="http://search.dangdang.com/?key=湖南文艺出版社" target="_blank">湖南文艺出版社</a></div>    

            <div class="biaosheng">五星评分：<span>2028次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥24.90</span>
                        <span class="price_r">¥49.80</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27904794');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_27904794" name="" href="javascript:showMsgBox('addto_favorlist_27904794',encodeURIComponent('27904794&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">15.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27895433.html" target="_blank"><img src="http://img3m3.ddimg.cn/5/23/27895433-1_l_3.jpg" alt="成都下水道 让我们灵魂激荡身体欢愉：一个男科医生的手记" title="成都下水道 让我们灵魂激荡身体欢愉：一个男科医生的手记"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27895433.html" target="_blank" title="成都下水道 让我们灵魂激荡身体欢愉：一个男科医生的手记">成都下水道 让我们灵魂激荡身体欢愉：一个男科医生的手记</a></div>    
    <div class="star"><span class="level"><span style="width: 99%;"></span></span><a href="http://product.dangdang.com/27895433.html?point=comment_point" target="_blank">5076条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=任黎明" title="任黎明（@成都下水道） 著，凤凰联动 出品" target="_blank">任黎明</a>（<a href="http://search.dangdang.com/?key=@成都下水道" title="任黎明（@成都下水道） 著，凤凰联动 出品" target="_blank">@成都下水道</a>） 著，<a href="http://search.dangdang.com/?key=凤凰联动" title="任黎明（@成都下水道） 著，凤凰联动 出品" target="_blank">凤凰联动</a> 出品</div>    
    <div class="publisher_info"><span>2019-07-01</span>&nbsp;<a href="http://search.dangdang.com/?key=天津科学技术出版社" target="_blank">天津科学技术出版社</a></div>    

            <div class="biaosheng">五星评分：<span>1892次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥24.00</span>
                        <span class="price_r">¥48.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e">电子书：<span class="price_n">¥19.90</span></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27895433');" class="listbtn_buy">加入购物车</a>
                        
                        <a name="" href="http://product.dangdang.com/1901109557.html" class="listbtn_buydz" target="_blank">购买电子书</a>
                        <a ddname="加入收藏" id="addto_favorlist_27895433" name="" href="javascript:showMsgBox('addto_favorlist_27895433',encodeURIComponent('27895433&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">16.</div>   
    <div class="pic"><a href="http://product.dangdang.com/25536034.html" target="_blank"><img src="http://img3m4.ddimg.cn/73/3/25536034-1_l_3.jpg" alt="锁脑：如何瞬间、深度、持久地影响他人" title="锁脑：如何瞬间、深度、持久地影响他人"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/25536034.html" target="_blank" title="锁脑：如何瞬间、深度、持久地影响他人">锁脑：如何瞬间、深度、持久地影响他人</a></div>    
    <div class="star"><span class="level"><span style="width: 98%;"></span></span><a href="http://product.dangdang.com/25536034.html?point=comment_point" target="_blank">23962条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=程志良" title="程志良" target="_blank">程志良</a></div>    
    <div class="publisher_info"><span>2018-10-20</span>&nbsp;<a href="http://search.dangdang.com/?key=机械工业出版社" target="_blank">机械工业出版社</a></div>    

            <div class="biaosheng">五星评分：<span>18919次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥44.20</span>
                        <span class="price_r">¥59.00</span>(<span class="price_s">7.5折</span>)
                    </p>
                    <p class="price_e">电子书：<span class="price_n">¥28.90</span></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('25536034');" class="listbtn_buy">加入购物车</a>
                        
                        <a name="" href="http://product.dangdang.com/1901129158.html" class="listbtn_buydz" target="_blank">购买电子书</a>
                        <a ddname="加入收藏" id="addto_favorlist_25536034" name="" href="javascript:showMsgBox('addto_favorlist_25536034',encodeURIComponent('25536034&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">17.</div>   
    <div class="pic"><a href="http://product.dangdang.com/25345990.html" target="_blank"><img src="http://img3m0.ddimg.cn/10/28/25345990-1_l_3.jpg" alt="间谍之死(拒绝好莱坞翻拍的年度作品，刺激程度超越《碟中谍》，更好看的悬疑推理小说，，更精彩的高科技犯罪！屏住呼吸，追击即将开始) 纤阅出品" title="间谍之死(拒绝好莱坞翻拍的年度作品，刺激程度超越《碟中谍》，更好看的悬疑推理小说，，更精彩的高科技犯罪！屏住呼吸，追击即将开始) 纤阅出品"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/25345990.html" target="_blank" title="间谍之死(拒绝好莱坞翻拍的年度作品，刺激程度超越《碟中谍》，更好看的悬疑推理小说，，更精彩的高科技犯罪！屏住呼吸，追击即将开始) 纤阅出品">间谍之死(拒绝好莱坞翻拍的年度作品，刺激程度超越《碟中谍》，更<span class="dot">...</span></a></div>    
    <div class="star"><span class="level"><span style="width: 100%;"></span></span><a href="http://product.dangdang.com/25345990.html?point=comment_point" target="_blank">28434条评论</a><span class="tuijian">99.9%推荐</span></div>    
    <div class="publisher_info">[美]<a href="http://search.dangdang.com/?key=丹·马里兰" title="[美]丹·马里兰 著，胡彭/吴晓菲/王一晴 译" target="_blank">丹·马里兰</a> 著，<a href="http://search.dangdang.com/?key=胡彭" title="[美]丹·马里兰 著，胡彭/吴晓菲/王一晴 译" target="_blank">胡彭</a>/<a href="http://search.dangdang.com/?key=吴晓菲" title="[美]丹·马里兰 著，胡彭/吴晓菲/王一晴 译" target="_blank">吴晓菲</a>/<a href="http://search.dangdang.com/?key=王一晴" title="[美]丹·马里兰 著，胡彭/吴晓菲/王一晴 译" target="_blank">王一晴</a> 译</div>    
    <div class="publisher_info"><span>2018-08-01</span>&nbsp;<a href="http://search.dangdang.com/?key=天津人民出版社" target="_blank">天津人民出版社</a></div>    

            <div class="biaosheng">五星评分：<span>23201次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥28.50</span>
                        <span class="price_r">¥57.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('25345990');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_25345990" name="" href="javascript:showMsgBox('addto_favorlist_25345990',encodeURIComponent('25345990&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">18.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27912223.html" target="_blank"><img src="http://img3m3.ddimg.cn/64/15/27912223-1_l_3.jpg" alt="戴建业 论中国古代的知识分类与典籍分类（国民教授带你摸透古代学问的路径！陈引驰、骆玉明、六神磊磊推荐！）" title="戴建业 论中国古代的知识分类与典籍分类（国民教授带你摸透古代学问的路径！陈引驰、骆玉明、六神磊磊推荐！）"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27912223.html" target="_blank" title="戴建业 论中国古代的知识分类与典籍分类（国民教授带你摸透古代学问的路径！陈引驰、骆玉明、六神磊磊推荐！）">戴建业 论中国古代的知识分类与典籍分类（国民教授带你摸透古代学<span class="dot">...</span></a></div>    
    <div class="star"><span class="level"><span style="width: 100%;"></span></span><a href="http://product.dangdang.com/27912223.html?point=comment_point" target="_blank">1123条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=戴建业" title="戴建业，果麦文化 出品" target="_blank">戴建业</a>，<a href="http://search.dangdang.com/?key=果麦文化" title="戴建业，果麦文化 出品" target="_blank">果麦文化</a> 出品</div>    
    <div class="publisher_info"><span>2019-08-01</span>&nbsp;<a href="http://search.dangdang.com/?key=上海文艺出版社" target="_blank">上海文艺出版社</a></div>    

            <div class="biaosheng">五星评分：<span>1118次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥29.00</span>
                        <span class="price_r">¥58.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e">电子书：<span class="price_n">¥26.00</span></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27912223');" class="listbtn_buy">加入购物车</a>
                        
                        <a name="" href="http://product.dangdang.com/1901139472.html" class="listbtn_buydz" target="_blank">购买电子书</a>
                        <a ddname="加入收藏" id="addto_favorlist_27912223" name="" href="javascript:showMsgBox('addto_favorlist_27912223',encodeURIComponent('27912223&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">19.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27916855.html" target="_blank"><img src="http://img3m5.ddimg.cn/43/22/27916855-1_l_3.jpg" alt="漂亮老师（能跟学生做朋友的老师才是学生喜欢的老师。赢得600万小读者喜爱的杨红樱成长小说，为老师家长和孩子搭通沟通的桥梁）" title="漂亮老师（能跟学生做朋友的老师才是学生喜欢的老师。赢得600万小读者喜爱的杨红樱成长小说，为老师家长和孩子搭通沟通的桥梁）"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27916855.html" target="_blank" title="漂亮老师（能跟学生做朋友的老师才是学生喜欢的老师。赢得600万小读者喜爱的杨红樱成长小说，为老师家长和孩子搭通沟通的桥梁）">漂亮老师（能跟学生做朋友的老师才是学生喜欢的老师。赢得600万小<span class="dot">...</span></a></div>    
    <div class="star"><span class="level"><span style="width: 100%;"></span></span><a href="http://product.dangdang.com/27916855.html?point=comment_point" target="_blank">1109条评论</a><span class="tuijian">100%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=杨红樱" title="杨红樱，果麦文化 出品" target="_blank">杨红樱</a>，<a href="http://search.dangdang.com/?key=果麦文化" title="杨红樱，果麦文化 出品" target="_blank">果麦文化</a> 出品</div>    
    <div class="publisher_info"><span>2019-08-01</span>&nbsp;<a href="http://search.dangdang.com/?key=作家出版社" target="_blank">作家出版社</a></div>    

            <div class="biaosheng">五星评分：<span>1104次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥22.30</span>
                        <span class="price_r">¥29.80</span>(<span class="price_s">7.5折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27916855');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_27916855" name="" href="javascript:showMsgBox('addto_favorlist_27916855',encodeURIComponent('27916855&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
    <li>
    <div class="list_num ">20.</div>   
    <div class="pic"><a href="http://product.dangdang.com/27908979.html" target="_blank"><img src="http://img3m9.ddimg.cn/87/27/27908979-1_l_11.jpg" alt="小王子绘本（作者基金会官方授权，畅销350万册小说同款绘本，儿童文学泰斗梅子涵推荐，37幅原创全彩跨页插画）【果麦经典】" title="小王子绘本（作者基金会官方授权，畅销350万册小说同款绘本，儿童文学泰斗梅子涵推荐，37幅原创全彩跨页插画）【果麦经典】"></a></div>    
    <div class="name"><a href="http://product.dangdang.com/27908979.html" target="_blank" title="小王子绘本（作者基金会官方授权，畅销350万册小说同款绘本，儿童文学泰斗梅子涵推荐，37幅原创全彩跨页插画）【果麦经典】">小王子绘本（作者基金会官方授权，畅销350万册小说同款绘本，儿童<span class="dot">...</span></a></div>    
    <div class="star"><span class="level"><span style="width: 96%;"></span></span><a href="http://product.dangdang.com/27908979.html?point=comment_point" target="_blank">1128条评论</a><span class="tuijian">99.8%推荐</span></div>    
    <div class="publisher_info"><a href="http://search.dangdang.com/?key=安托万·德·圣埃克苏佩里" title="安托万·德·圣埃克苏佩里，绘 豆子，果麦文化 出品" target="_blank">安托万·德·圣埃克苏佩里</a>，绘 <a href="http://search.dangdang.com/?key=豆子" title="安托万·德·圣埃克苏佩里，绘 豆子，果麦文化 出品" target="_blank">豆子</a>，<a href="http://search.dangdang.com/?key=果麦文化" title="安托万·德·圣埃克苏佩里，绘 豆子，果麦文化 出品" target="_blank">果麦文化</a> 出品</div>    
    <div class="publisher_info"><span>2019-07-01</span>&nbsp;<a href="http://search.dangdang.com/?key=山东画报出版社" target="_blank">山东画报出版社</a></div>    

            <div class="biaosheng">五星评分：<span>1074次</span></div>
                      
    
    <div class="price">        
        <p><span class="price_n">¥29.50</span>
                        <span class="price_r">¥59.00</span>(<span class="price_s">5.0折</span>)
                    </p>
                    <p class="price_e"></p>
                <div class="buy_button">
                          <a ddname="加入购物车" name="" href="javascript:AddToShoppingCart('27908979');" class="listbtn_buy">加入购物车</a>
                        
                        <a ddname="加入收藏" id="addto_favorlist_27908979" name="" href="javascript:showMsgBox('addto_favorlist_27908979',encodeURIComponent('27908979&amp;platform=3'), 'http://myhome.dangdang.com/addFavoritepop');" class="listbtn_collect">收藏</a>
     
        </div>

    </div>
  
    </li>    
    
</ul>


上面这段页面代码我们很容易找出规律来，就是 ul 标签下的多个 li 标签 我们要的内容就在每个 li标签的 div 标签里！
下面我们用 BeautifulSoup 来做一次爬取
首先安装模块： pip install beautifulsoup4
并且安装一下解析器：pip install lxml
引入模块：from bs4 import beautifulsoup
#我们重写一下方法：
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
	
	
	
def bs4test(html):
	soup = BeautifulSoup(html, 'lxml')
	ul_tag = soup.find('ul', 'bang_list_mode')
	for tag in ul_tag.find_all('li'):
		data_div = tag.find_all('div')
		yield {
			'range': data_div[0].string[0],
			'image': data_div[1].img['src'],
			'title': data_div[2].a['title'],
			'comment': data_div[3].a.string,
			'recommend': data_div[3].find('span', 'tuijian').string,
			'author': data_div[4].a['title'],
			'star_times': '标星' + data_div[6].span.string,
			'price': '价格：' + data_div[7].p.span.string
		}

#其它还是不变的
#发现其中的代码少了很多，而且也不用写复杂的讨厌的长长长正则字符串了
#BeautifulSoup 模块学习地址 https://www.crummy.com/software/BeautifulSoup/bs4/doc.zh/
'''


	
	
	
	
	
	
