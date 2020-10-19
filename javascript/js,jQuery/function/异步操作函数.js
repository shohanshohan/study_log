<script>
			var items = [1,2,3,4,5,6];
			var results = [];
      //异步函数
			function async(arg, callback) {
				console.log('arg is ' + arg + ' 1 seconds return.');
				setTimeout(function() { callback(arg ** 2); }, 1000);
			}
			function final(value) {
				console.log('finished ' + value);
			}
			//串行执行，即按顺序执行
			function series(item) {
				if(item) {
					async(item, function(result) {
						results.push(result);
						return series(items.shift());
					});
				} else {
					return final(results);
				}
			}
			//series(items.shift());
			
			//并行执行，异步任务同时执行（任务量多时消耗资源大，优点是效率高）
			// items.forEach(function(item) {
			// 	async(item, function(result){
			// 		results.push(result);
			// 		if(results.length === items.length) {
			// 			final(results);
			// 		}
			// 	});
			// });
			
			//-------并行与串行执行，limit规定每次执行异步操作最大任务量--------//
			var running = 0;
			var limit = 2;
			function launcher() {
				while(running < limit && items.length > 0) {
					var item = items.shift();
					async(item, function(result){
						results.push(result);
						running--;
						if(items.length > 0) {
							launcher();
						} else if (running == 0) {
							final(results);
						}
					});
					running++;
					console.log(running);
				}
			}
			launcher();
		</script>
