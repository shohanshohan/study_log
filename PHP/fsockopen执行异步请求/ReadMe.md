# 有时候我们想要执行一些耗时的业务，想要做一个异步的代码执行操作
## 就是先完成当前的程序逻辑，当符合某个条件时，让耗时的业务去请求另外的地方处理，而不要影响当前脚本的执行时间和返回信息

### 要做到异步执行操作，就要用到 fsockopen

我们来模拟一下，在 localhost 根目录下，创建两个脚本，request.php 和 receiveApi.php
一个用来执行当前业务，一个是异步请求接口
在浏览器地址输入 localhost/request.php  执行 脚本request.php   
打印出信息： 
start
spend 1 seconds

说明这个脚本执行时间为 1 秒，打印了返回信息

过几秒钟查看根目录下多了一个 test.txt 文件，里面已写入在 receiveApi.php 脚本中制定的内容：
test request , this script spend 5 seconds, params: {"name":"test-name","age":"18"}

说明异步业务执行时间为 5 秒，并且正确接收到了传送的参数信息

是不是很简单呢！






