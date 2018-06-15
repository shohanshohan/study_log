这里用到 navigator.userAgent
方法1：if(/ipad|iphone/i.test(navigator.userAgent)) 用正则匹配，正确匹配返回true
方法2：if(navigator.userAgent.toLowerCase().indexOf('qq') > -1) 用字符串去查询，如果不存在则返回 -1


