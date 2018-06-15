function getQueryVariable(variable)
{
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}
使用实例
url 实例：
http://www.runoob.com/index.php?id=1&image=awesome.jpg
调用 getQueryVariable("id") 返回 1。
调用 getQueryVariable("image") 返回 "awesome.jpg"。

// 下面这个函数返回整个url参数数组
       function urlArgs(){
		var args = {};
		var query = location.search.substring(1); // 查找到查询串，并去掉‘?’
		var pairs = query.split('&');
		for(var i=0; i<pairs.length; i++){
			var pos = pairs[i].indexOf('=');
			if(pos == -1) continue;
			var name = pairs[i].substring(0,pos);
			var value = pairs[i].substring(pos+1);
			value = decodeURIComponent(value);
			args[name] = value;
		}
		return args;
	}
