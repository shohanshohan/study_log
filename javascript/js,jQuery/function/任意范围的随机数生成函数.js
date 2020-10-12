//取任意两个整数之间的随机数，不包含最大数，如果要生成 1-10（包含最小最大值则要传参数 1 和 11）
function randInt(min, max) {
	return Math.floor(Math.random() * (max - min) + min);
	//如果不包含最小值则是  return Math.ceil(Math.random() * (max - min) + min);
}

//包含最小值 和 最大值的随机整数函数
function rangeInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}
