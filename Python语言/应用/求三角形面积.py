#=========================
# 已知三条边的长度，求三角形面积
# 假设三角形三边长度为 a, b, c
# 利用海伦公式：
# p = (a + b +c) / 2
# area = √p * (p-a) * (p-b) * (p-c)
#=========================
from math import sqrt

class Triangle():
	
	def __init__(self, a, b, c):
		self._a = a
		self._b = b 
		self._c = c 
	
	# 用静态方法先判断三边长度是否可以构成三角形
	@staticmethod
	def is_valid(a, b, c):
		return a + b > c and b + c > a and a + c > b
	
	def perimeter(self):
		return self._a + self._b + self._c
	
	def area(self):
		half = self.perimeter() / 2
		return sqrt(half * (half - self._a) * (half - self._b) * (half - self._c))
		
def main():
	a, b, c = 3, 4, 5
	if Triangle.is_valid(a, b , c):
		t = Triangle(a, b, c)
		print(t.area())
	else: 
		print('无法构成三角形')
		
if __name__ == '__main__':
	main()
