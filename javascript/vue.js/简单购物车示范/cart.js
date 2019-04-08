var app = new Vue({
  el: '#app',
  data: {
    goods: [
      {ID: 1, name: 'iPhone7', price: 6188, num: 1},
      {ID: 2, name: 'iPad Pro', price: 5888, num: 1},
      {ID: 3, name: 'MacBook Pro', price: 21688, num: 1}
    ],
    checkedAll: false, //是否全选
    itemCheckedGoods: [] //选中的商品
  },
  computed: {
    amount: function(){
      var amount = 0;
      if(this.checkedAll){ //如果全选，计算所有商品的总价
        for(item in this.goods){
          amount += this.goods[item].price * this.goods[item].num;
        }
      }else{// 计算选中的商品总价
        for(item in this.itemCheckedGoods){
          amount += this.itemCheckedGoods[item].price * this.itemCheckedGoods[item].num;
        }
      }
      //三位数分隔符
      return amount.toString().replace(/\B(?=(\d{3})+$)/g, ',');
    },
    itemChecked: function(){ //全选切换
      return this.checkedAll ? true : false
    }
  },
  methods: {
    //移除
    deleteHandle: function(index){
      this.goods.splice(index,1);
    },
    //添加数量
    plusHandle: function(index){
      this.goods[index].num++;
    },
    //减少数量
    reduceHandle: function(index){
      if(this.goods[index].num>0){
        this.goods[index].num--;
      }
    },
    //全选切换
    selectAllHandle: function(){
      this.checkedAll = this.checkedAll ? false : true;
    },
    //单个商品选中切换事件
    itemCheckedHandle: function(index, event){
      if(event.target.checked){
        this.itemCheckedGoods.push(this.goods[index]); //添加到选中商品
      }else{ //从选中商品中移除
        this.itemCheckedGoods.splice(this.itemCheckedGoods.indexOf(this.goods[index]), 1);
      }
    }
  }
});
