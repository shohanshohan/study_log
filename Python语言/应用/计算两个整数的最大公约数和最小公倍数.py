x = int(input('x = '))
y = int(input('y = '))

if x > y:
  x, y = y, x
  
for j in range(x, 0, -1):
  if x % j == 0 and y % j == 0:
    print('{}和{}的最大公约数是：{}'.format(x, y, j))
    print('{}和{}的最小公倍数是：{}'.format(x, y, x*y//j))
    break
