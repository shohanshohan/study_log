// 设置倒计时
    function verifySettime(obj){
        var countdown=120;
        settime(obj);
        function settime(obj) {
            if (countdown == 0) {
                $(obj).attr("disabled",false);
                $(obj).val("获取验证码");
                countdown = 120;
                return;
            } else {
                $(obj).attr("disabled",true);
                $(obj).val(countdown + 's');
                countdown--;
            }
            setTimeout(function(){
              settime(obj) 
            },1000)
        }
    }
