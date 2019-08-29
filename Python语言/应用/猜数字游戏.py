import random

answer = random.randint(1, 100)
counter = 0
while True:
	counter += 1
	num = int(input('请输入一个整数：'))
	if num < answer:
		print('数字太小了')
	elif num == answer:
		print('恭喜你，猜对了')
		break
	else:
		print('数字太大了')
print('你总共猜了{}次'.format(counter))
if counter > 7:
	print('超过7次还没猜中！智商堪忧')
	

