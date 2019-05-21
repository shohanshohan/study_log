const BALLS_COUNT = 225;
const BALL_SIZE_MIN = 10;
const BALL_SIZE_MAX = 20;
const BALL_SPEED_MAX = 7;
//设定画布
const canvas = document.querySelector('canvas')
const ctx = canvas.getContext('2d')

//设定画布的长宽
const width = canvas.width = window.innerWidth;
const height = canvas.height = window.innerHeight;

//生成随机数的函数
function random(min, max) {
  return Math.floor(Math.random() * (max - min)) + min;
}

//生成随机颜色的函数
function randomColor() {
  return 'rgb(' +
         random(0, 255) + ', ' +
         random(0, 255) + ', ' +
         random(0, 255) + ')';
}

//建立小球模型
//x 与 y 坐标 从浏览器左上角 0开始
//velX 与 velY 小球的水平和竖直速度，通过每一帧给小球的x和y坐标加一次这些值让这些小球运动
//color 每一个小球的颜色
//size 每一个小球的大小，以像素为单位
function Ball(x, y, velX, velY, color, size) {
  this.x = x;
  this.y = y;
  this.velX = velX;
  this.velY = velY;
  this.color = color;
  this.size = size;
}

//画小球，给小球原型加上draw 
Ball.prototype.draw = function() {
  ctx.beginPath();
  ctx.fillStyle = this.color;
  ctx.arc(this.x, this.y, this.size, 0, 2 * Math.PI);
  ctx.fill();
}

//测试画一个小球
/*var testBall = new Ball(50, 100, 4, 4, 'blue', 10);
testBall.x = 100;
testBall.y = 100;
testBall.size = 50;
testBall.color = 'green';
testBall.draw();*/

//给小球原型加上一个update()方法, 更新小球的数据
//检查小球是否碰到画布的边缘。如果碰到则反转小球的速度方向让它反向移动
Ball.prototype.update = function() {
  if((this.x + this.size) >= width) {
    this.velX = -(this.velX);
  }

  if((this.x - this.size) <= 0) {
    this.velX = -(this.velX);
  }

  if((this.y + this.size) >= height) {
    this.velY = -(this.velY);
  }

  if((this.y - this.size) <= 0) {
    this.velY = -(this.velY);
  }

  this.x += this.velX;
  this.y += this.velY;
}

//碰撞检测
Ball.prototype.collisionDetect = function() {
  for(var j = 0; j < balls.length; j++){
    if(!(this === balls[j])){
      var dx = this.x - balls[j].x;
      var dy = this.y - balls[j].y;
      var distance = Math.sqrt(dx * dx + dy * dy); //这个根据三角形的勾股定理（x2 + y2 = z2）得出两小球的中心距离

      //当两个小球的中心距离小于它们的半径之和时说明已碰撞了，将两个小球的颜色都设置成随机的一种
      if(distance < this.size + balls[j].size){
        balls[j].color = this.color = randomColor();
      }
    }
  }
}


//在画布上加上一些小球，并让他们动起来
var balls = [];



//运动循环
function loop() {
  ctx.fillStyle = 'rgba(0, 0, 0, 0.25)';
  ctx.fillRect(0, 0, width, height);

  while (balls.length < BALLS_COUNT) { //保证小球数量
    const size = random(BALL_SIZE_MIN, BALL_SIZE_MAX)
    var ball = new Ball(
      random(0, width),
      random(0, height),
      random(-BALL_SPEED_MAX, BALL_SPEED_MAX),
      random(-BALL_SPEED_MAX, BALL_SPEED_MAX),
      randomColor(),
      size
    )
    balls.push(ball);
  }

  for (var i = 0; i < balls.length; i++) {
    balls[i].draw(); //绘制出每一个小球
    balls[i].update();//检查是否碰到画布边缘
    balls[i].collisionDetect();//检测是否碰撞
  }

  //要求浏览器在下次重绘之前调用指定的回调函数更新动画
  requestAnimationFrame(loop);
}

//启动循环
loop();
