#encoding=utf-8  
import threading
import time
import math

class myThread (threading.Thread):
    def __init__(self, threadID, name):
        threading.Thread.__init__(self)
        self.threadID = threadID
        self.name = name
    def run(self):
        print ("开启任务：" + self.name)
        process_data(self.name, 1)
        print ("退出任务：" + self.name)

def log(timeStr, content):
    ''' 记录日志信息 '''
    fileName = './runtime/' + timeStr + '.log'
    file = open(fileName, 'a')
    file.write(content + "\r\n")
    file.close()

def process_data(theadName, delay):
	print('开始执行：{}'.format(theadName))
	log('20200102', theadName)
	time.sleep(delay)
	print('{} 执行结束'.format(theadName))
		


print('start: ' + time.strftime("%H:%M:%S", time.localtime()))	
threads = []

for i in range(5):
    thread1 = myThread(i,'Thread-' + str(i))
    thread1.start()
    threads.append(thread1)

for th in threads:
    th.join()
	
print('退出线程')
print('end: ' + time.strftime("%H:%M:%S", time.localtime()))






