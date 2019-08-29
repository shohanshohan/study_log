# 阿姆斯特朗数又叫水仙花数，是指每一位数的立方之和刚好等于这个数的一个三位数整数，或叫三位自幂数
num_list = []
for i in range(100, 1000):
	x = i // 100 #百位数
	y = i // 10 % 10 #十位数
	z = i % 10 #个位数
	if x**3 + y**3 + z**3 == i:
		num_list.append(i)

print('三位数水仙花数是：', num_list)
	

# 同理，我们求四位自幂数，又叫四叶玫瑰数
num_list = []
for i in range(1000, 10000):
	j = i // 1000 #千位数
	x = i // 100 % 10 #百位数
	y = i // 10 % 10 #十位数
	z = i % 10 #个位数
	if j**4 + x**4 + y**4 + z**4 == i:
		num_list.append(i)

print('四叶玫瑰数是：', num_list)
	

