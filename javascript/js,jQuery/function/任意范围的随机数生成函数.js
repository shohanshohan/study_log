//取任意两个整数之间的随机数，不包含最大数，如果要生成 1-10（包含最小最大值则要传参数 1 和 11）
function randNum(min, max) {
	return Math.floor(Math.random() * (max - min) + min);
}
