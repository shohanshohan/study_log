
from random import randint, sample

def display(balls):
	for index, ball in enumerate(balls):
		if index == len(balls) - 1:
			print('|', end=' ')
		print('{:02d}'.format(ball), end=' ')
	print()
	
def random_select():
	red_balls = [x for x in range(1, 34)]
	selected_balls = []
	selected_balls = sample(red_balls, 6)
	selected_balls.sort()
	selected_balls.append(randint(1, 16))
	return selected_balls
	
n = int(input('机选几注：'))
for x in range(n):
	display(random_select())
