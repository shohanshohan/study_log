from functools import wraps

class logit(object):
	def __init__(self, logfile='out.log'):
		self.logfile = logfile
	
	def __call__(self, func):
		@wraps(func)
		def wrapped_function(*args, **kwargs):
			log_str = func.__name__ + ' was called'
			print(log_str)
			with open(self.logfile, 'a') as openfile:
				#打印日志到指定文件
				openfile.write(log_str + '\n')
			#现在，发送一个通知
			self.notify()
			return func(*args, **kwargs)
		return wrapped_function
		
	def notify(self):
		#logit只打印日志，不做别的
		pass
'''	
#就像装饰器函数一样调用	
@logit()
def myfunc1():
	pass

myfunc1()
'''

#现在，我们给logit创建子类，来添加email的功能(虽然email这个话题不会在这里展开)。
class email_logit(logit):
	'''
	一个logit的实现版本，可以在函数调用时发送email给管理员
	'''
	def __init__(self, email='xxx@yyy.com', *args, **kwargs):
		self.email = email
		super(email_logit, self).__init__(*args, **kwargs)
	
	def notify(self):
		#发送一封email到self.email
		#具体实现...
		pass
#从现在起，@email_logit将会和@logit产生同样的效果，但是在打日志的基础上，还会多发送一封邮件给管理员


	
