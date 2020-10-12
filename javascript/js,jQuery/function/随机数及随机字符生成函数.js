//取任意两个整数之间的随机数，不包含最大数，如果要生成 1-10（包含最小最大值则要传参数 1 和 11）
function randInt(min, max) {
	return Math.floor(Math.random() * (max - min) + min);
	//如果不包含最小值则是  return Math.ceil(Math.random() * (max - min) + min);
}

//包含最小值 和 最大值的随机整数函数
function rangeInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}


//随机字符生成函数，可选择字符长度
function randomStr(length) {
	let alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	alphabet += 'abcdefghijklmnopqrstuvwxyz';
	alphabet += '0123456789';
	let str = '';
	for (let i=0; i<length; i++) {
		str += alphabet.substr(Math.floor(Math.random() * alphabet.length), 1);
	}
	return str;
}
