thinkphp5  接支付宝手机wap支付
首先下载一个php的demo,  下载地址：https://docs.open.alipay.com/54/106682/   (选择PHP版本)
在 thinkphp 框架下的extend 目录下新建文件夹 alipay
将下载好的demo解压，把角标文件放入 apipay目录下 （最外层目录不要）

新建模块 pay
新建文件 pay/controller/Alipay.php   和 pay/controller/Alinotify.php
一个是请求支付页面，一个是做服务器回调
然后在 route.php 文件配置路由访问：
//页面支付相关
'alipay' => 'pay/Alipay/index', //支付宝充值页面
'aliwappage' => 'pay/Alipay/wappage', //支付宝支付页面
'paycheck' => 'pay/Alipay/check', //支付宝支付提交参数
'aliwapnotify' => 'pay/Alinotify/aliwapnotify', //支付宝H5支付回调地址

在 pay 模块下新建一个配置文件 config.php  (如果你的配置目录是单独的外面config目录，则新建 config/pay/config.php文件)
在里面配置 aliwapPay 的一些请求参数 （详情见config.php文件）

如果想要传一些在回调时的回传参数（可以多个），则要带上 passback_params 参数，但这个参数记得要使用 http_build_query($paramsData) 转换

但在使用封装的demo示例时，发现没有这个 passback_params 的参数封装，这时候我们要给它加上
打开文件 extend\alipay\wappay\buildermodel\AlipayTradeWapPayContentBuilder.php
加上 如下代码：
// 回传参数
private $passback_params;
//......
public function getPassbackParams()
{
  return $this->passback_params;
}

public function setPassbackParams($params)
{
  $this->passback_params = $params;
  $this->bizContentarr['passback_params'] = $params;
}



配置密钥字符时：只保留字符串，不要留下 -----BEGIN RSA PRIVATE KEY----- 这种标记
