//加载jquery
// 窗口变化
  var winHeight = $(window).height();
  $(window).resize(function() {
    var thisHeight = $(this).height();
    if(winHeight - thisHeight > 50){
      $('form').css('top','-95%');
    }else{
      $('form').css('top','-51%%');
    }
  });
