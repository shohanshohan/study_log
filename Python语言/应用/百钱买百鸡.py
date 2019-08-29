#================
# 我国古代数学家张丘建在《算经》一书中提出的数学问题：鸡翁一值钱五，鸡母一值钱三，鸡雏三值钱一。
# 百钱买百鸡，问鸡翁、鸡母、鸡雏各几何？
#================
chicken = 100 # 鸡的总数
money = 100 # 钱的总数
price1 = 5 #鸡翁价格钱五
price2 = 3 #鸡母价格钱三
price3 = 1 #鸡雏价格钱一（三只）

#---------------
# 思路：
# 假设鸡翁的数量为 x, 鸡母的数量为 y 
# 因为我们知道鸡的总数为 100 ,所以 x 或 y 的值范围 一定在 0~100 之间
# 用两个循环算出所有鸡的总价钱等于 100 ，得出符合条件的组合
#---------------
for x in range(0, chicken+1):
	money1 = x * price1 # 买鸡翁花的钱
	for y in range(0, chicken+1):
		money2 = y * price2 # 买鸡母花的钱
		money3 = (chicken-x-y) * price3 / 3 # 买鸡雏花的钱
		if money1 + money2 + money3 == money:
			print('鸡翁：{}只，鸡母：{}只，鸡雏：{}只'.format(x, y, chicken-x-y))


#-------------------
# 用上面方法还可以算出千钱买千鸡的组合，只需要把鸡和钱的数量改成1000
# 价格不变，甚至可以求任意鸡总数 = 钱总数的组合了
#-------------------


#--------------------
# 上面的方法还可以再改进一下
# 我们取的鸡翁和鸡母的范围还可以缩小一点，这样性能更好
# 因为我们知道钱的总数和价格，所以单个类别的鸡的数量乘以价格不能大于总钱
# 所以鸡翁的数量范围是 range(0, 100//5) 即0~20，同理鸡母的数量范围是 0~33
#--------------------
for x in range(0, money // price1):
	money1 = x * price1 # 买鸡翁花的钱
	for y in range(0, money // price2):
		money2 = y * price2 # 买鸡母花的钱
		money3 = (chicken-x-y) * price3 / 3 # 买鸡雏花的钱
		if money1 + money2 + money3 == money:
			print('鸡翁：{}只，鸡母：{}只，鸡雏：{}只'.format(x, y, chicken-x-y))
			
			
# 最精简的版本是下面这样，只要四行代码
for x in range(0, money // price1):
	for y in range(0, money // price2):
		if x*price1 + y*price2 + (chicken-x-y)*price3/3 == money:
			print('鸡翁：{}只，鸡母：{}只，鸡雏：{}只'.format(x, y, chicken-x-y))
