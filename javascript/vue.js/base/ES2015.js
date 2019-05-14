var odds = events.map(v => v +1);
var nums = events.map((v, i) => v + i); //如果转换成函数形式,应该是怎样的?

nums.forEach(v => {
	if(v % 5 === 0){
		fives.push(v)
	}
})

var bob = {
	_name: "Bob",
	_friends: [],
	printFriends() {
		this._friends.forEach(f => 
		  console.log(this._name + "knows" + f)
		)
	}
}

function square() {
	let example = () => {
		let numbers = [];
		for(let number of arguments){
			numbers.push(number * number)
		}
		return numbers;
	}
	return exmple();
}

square(2, 4, 6);


class SkinnedMesh extends THREE.Mesh {
	constructor(geometry, materials){
		super(geometry, materials)
	}
	update(camera){
		super.update()
	}
	static defaultMatrix(){
		return new THREE.Matrix4();
	}
}
