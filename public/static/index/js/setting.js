// 退出登录 
$('.exit').click(function(){
    $.ajax({
        url: 'logout',
        type: 'POST',
        dataType: 'JSON',
        success: function(data){
            console.log(data);
            location.href = 'login';
        },
        error: function(){
            console.log('error');
        }
    })
})
// 清除缓存
$('.clearing').click(function(){
    $.ajax({
        url:'www.haorooms.com',
        dataType:'json',
        async: false,
        beforeSend :function(xmlHttp){ 
            xmlHttp.setRequestHeader("If-Modified-Since","0"); 
            xmlHttp.setRequestHeader("Cache-Control","no-cache");
        },
        success:function(response){
            //操作
        }
    });
})

// 修改登录密码

// 显示修改登录密码 弹窗
$('.reset-password-li').click(function(){
    $('.reset-pwd-pop').show();
    $('.wrapper').hide();
})
$('.reset-pwd-back').click(function(){
    $('.reset-pwd-pop').hide();
    $('.wrapper').show();
})
$('.reset-pwd-btn').click(function(){
    var reset = $('.reset-pwd-input').val();
    var repeat = $('.repeat-pwd-input').val()
    if(reset !== '' && repeat !== '' && reset === repeat){
        $.ajax({
            url: 'update_password',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'password': repeat
            },
            success: function(res){
                console.log(res);
                if(res.status == 1){
                    layer.open({
                        skin: 'msg',
                        content: res.info,
                        time: 1
                    })
                    setTimeout(function(){
                        location.href = 'login';
                    }, 1100)
                }
            },
            error: function(){
                console.log('error');
            }
        })
    }else if(reset !== repeat){
        layer.open({
            skin: 'msg',
            content: '前后密码不一致',
            time: 1
        })
    }else{
        layer.open({
            skin: 'msg',
            content: '请填写完整',
            time: 1
        })
    }
})

// 修改支付密码
// 验证码倒计时
function buttonCountdown($el, msNum, timeFormat) {
    var text = $el.data("text") || $el.text(),
            timer = 0;
    $el.prop("disabled", true).addClass("disabled")
            .on("bc.clear", function () {
                clearTime();
            });
    (function countdown() {
        var time = showTime(msNum)[timeFormat];
        $el.text(time + '后失效');
        if (msNum <= 0) {
            msNum = 0;
            clearTime();
        } else {
            msNum -= 1000;
            timer = setTimeout(arguments.callee, 1000);
        }
    })();
    function clearTime() {
        clearTimeout(timer);
        $el.prop("disabled", false).removeClass("disabled").text(text);
    }
    function showTime(ms) {
        var d = Math.floor(ms / 1000 / 60 / 60 / 24),
                h = Math.floor(ms / 1000 / 60 / 60 % 24),
                m = Math.floor(ms / 1000 / 60 % 60),
                s = Math.floor(ms / 1000 % 60),
                ss = Math.floor(ms / 1000);
        return {
            d: d + "天",
            h: h + "小时",
            m: m + "分",
            ss: ss + "秒",
            "d:h:m:s": d + "天" + h + "小时" + m + "分" + s + "秒",
            "h:m:s": h + "小时" + m + "分" + s + "秒",
            "m:s": m + "分" + s + "秒"
        };
    }
    return this;
}
// 显示修改支付密码 弹窗
$('.reset-payment-li').click(function(){
    $('.reset-payment-pop').show();
    $('.wrapper').hide();
})
$('.reset-payment-back').click(function(){
    $('.reset-payment-pop').hide();
    $('.wrapper').show();
})
$('.get-code').click(function(){
    var phone = $('.phone').val();
    var reg = /^1[34578]\d{9}$/;
    if(phone!== '' && phone.match(reg)){
        $.ajax({
            url: 'sendMobileCodes',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'mobile': phone
            },
            success: function(data){
                console.log(data);
                if(data.status == 1){
                    layer.open({
                        skin: 'msg',
                        content: data.info,
                        time: 1
                    })
                    buttonCountdown($(this), 1000 * 60 * 1, "ss");
                }else if(data.status == 0){
                    layer.open({
                        skin: 'msg',
                        content: data.info,
                        time: 1
                    })
                }
            },
            error: function(){
                console.log('error');
            }
        })
    }else{
        layer.open({
            skin: 'msg',
            content: '号码格式不正确',
            time: 1
        })
    }
})
$('.phone-code').blur(function(){
    if($(this).val() !== ''){
        $('.reset-payment-btn').attr('disabled', false);
    }
})
$('.reset-payment-btn').click(function(){
    var phone = $('.phone').val();
    var phoneCode = $('.phone-code').val();
    var newPwd = $('.new-pwd').val();
    var repeatPwd = $('.repeat-pwd').val();
    if(newPwd !== repeatPwd){
        layer.open({
            skin: 'msg',
            content: '两次输入密码不一致',
            time: 1
        })
    }else{
        $.ajax({
            url: 'pay_password_update',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'phone': phone,
                'phoneCode': phoneCode,
                'newPwd': newPwd,
                'repeatPwd': repeatPwd
            },
            success: function(data){
                console.log(data);
                if(data.status == 1){
                    layer.open({
                        skin: 'msg',
                        content: data.info,
                        time: 1
                    })
                }
            },
            error: function(){
                console.log('error');
            }
        })
    }
})

// 选择角色
// 显示修改支付密码 弹窗
$('.change-role-li').click(function(){
    $('.change-role-pop').show();
    $('.wrapper').hide();
})
$('.change-role-back').click(function(){
    $('.change-role-pop').hide();
    $('.wrapper').show();
})

$('.change-role-pop').on('change', 'input', function(){
    var role = $(this).attr('id');
    $('.change-role-btn').click(function(){
        if(role == 'customer'){
            location.href = 'my_index';
        }else if(role == 'supplier'){
            location.href = 'sell_my_index';
        }
    })
})

// 判断是否是商家还是只是车主（ajax）（隐藏切换角色的按钮）
$.ajax({
    url: 'is_business',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
    },
    error: function(){
        console.log('error');
    }
})
