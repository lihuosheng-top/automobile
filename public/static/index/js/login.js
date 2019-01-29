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

// 注册
$('.register').click(function(){
    $('.wrapper').css('display', 'none');
    $('.register-pop').css('display', 'block');
})
// 注册发送验证码
$('.reg-send-code').click(function(){
    var phone = $('.reg-phone-num').val();
    var reg = /^1[34578]\d{9}$/;
    var that = this;
    if(phone!== '' && phone.match(reg)){
        $.ajax({
            url: 'sendMobileCode',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'mobile': phone
            },
            success: function(data){
                console.log(data);
                if(data.status == 0){
                    layer.open({
                        skin: 'msg',
                        content: data.info,
                        time: 1
                    })
                }else{
                    layer.open({
                        skin: 'msg',
                        content: data.info,
                        time: 1
                    })
                    buttonCountdown($(that), 1000 * 60 * 1, "ss");
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
            time: .8
        })
    }
})

$('#agree-input').change(function(){
    if($(this)[0].checked){
        if($('.reg-message-code').val() !== ''){
            $('.reg-confirm').attr('disabled', false);
        }
    }
})
$('.reg-message-code').blur(function(){
    if($(this).val() !== ''){
        if($('#agree-input')[0].checked){
            $('.reg-confirm').attr('disabled', false);
        }
    }
})
// 注册确定按钮
$('.reg-confirm').click(function(){
    var mobile = $('.reg-phone-num').val(),
        mobile_code = $('.reg-message-code').val(),
        password = $('.reg-psw').val();
        confirm_password = $('.reg-psw').val();
        invitation = $('.reg-invite-code').val();

    $.ajax({
        url: 'doRegByPhone',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'mobile': mobile,
            'mobile_code': mobile_code,
            'password': password,
            'confirm_password': confirm_password,
            'invitation': invitation
        },
        success: function(data){
            console.log(data);
            if(data.status == 0){
                layer.open({
                    skin: 'msg',
                    content: data.info,
                    time: 1
                })
            }else{
                layer.open({
                    skin: 'msg',
                    content: '注册成功',
                    time: 1
                })
                setTimeout(function(){
                    $('.register-pop').css('display', 'none');
                    $('.wrapper').css('display', 'block');
                },1000)
            }
        },
        error: function(){
            console.log('error');
        }
    })
})

// 登录
$('.login').click(function(){
    $('.wrapper').css('display', 'block');
    $('.register-pop').css('display', 'none');
})
$('.reg-back').click(function(){
    $('.wrapper').css('display', 'block');
    $('.register-pop').css('display', 'none');
})
$('.login-btn').click(function(){
    var account = $('.phone-num').val(),
        passwd = $('.password').val();
    $.ajax({
        url: 'Dolog',
        type: 'GET',
        dataType: 'JSON',
        data: {
            'account': account,
            'passwd': passwd
        },
        success: function(data){
            console.log(data);
            if(data.status == 0){
                layer.open({
                    skin: 'msg',
                    content: data.info,
                    time: .8
                })
            }else if(data.status === 2){
                layer.open({
                    skin: 'msg',
                    content: data.info,
                    time: .8
                })
                setTimeout(function(){
                    location.href = 'express_wait_for_order';
                    Android.login(account);
                },1000)
            }else{
                layer.open({
                    skin: 'msg',
                    content: data.info,
                    time: .8
                })
                setTimeout(function(){
                    location.href = 'my_index';
                    Android.login(account);
                },1000)
            }
        },
        error: function(){
            console.log('error');
        }
    })
})

// 显示忘记密码
$('.forget-psw').click(function(){
    $('.wrapper').hide()
    $('.find-password').show();
})
$('.find-back').click(function(){
    $('.wrapper').show();
    $('.find-password').hide();
})
$('.message-code').blur(function(){
    if($(this).val() !== ''){
        $('.find-confirm').attr('disabled', false);
    }
})

// 忘记密码 发送验证码
$('.send-code').click(function(){
    var mobile = $('.find-phone-num').val();
    var reg = /^1[34578]\d{9}$/;
    if(mobile!== '' && mobile.match(reg)){
        buttonCountdown($(this), 1000 * 60 * 1, "ss");
        $.ajax({
            url: 'sendMobileCodeByPhone',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'mobile': mobile
            },
            success: function(data){
                console.log(data);
            },
            error: function(){
                console.log('error');
            }
        })
    }else{
        layer.open({
            skin: 'msg',
            content: '号码格式不正确',
            time: .8
        })
    }
})
// 找回密码 确定按钮
$('.find-confirm').click(function(){
    var phone_number = $('.find-phone-num').val(),
        password = $('.find-psw').val(),
        password_second = $('.find-repeat-psw').val(),
        code = $('.message-code').val();
    if(password == password_second){
        $.ajax({
            url: 'find_password_by_phone',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'phone_number': phone_number,
                'password': password,
                'password_second': password_second,
                'code': code
            },
            success: function(data){
                if(data.status == 0){
                    layer.open({
                        skin: 'msg',
                        content: data.info,
                        time: .8
                    })
                }else if(data.status == 1){
                    layer.open({
                        skin: 'msg',
                        content: data.info,
                        time: .8
                    })
                    setTimeout(function(){
                        $('.find-password').hide();
                        $('.wrapper').show();
                    }, 1000)
                }
            },
            error: function(){
                console.log('error');
            }
        })
    }else{
        layer.open({
            skin: 'msg',
            content: '两次输入密码不一致',
            time: .8
        })
    }
})

// 记住密码
$(document).ready(function(){
    var oPhoneNum = document.getElementsByClassName('phone-num')[0];
    var oPassword = document.getElementsByClassName('password')[0];
    var oRemember = document.getElementById('remember-input');
    //页面初始化时，如果帐号密码cookie存在则填充
    if (getCookie(btoa('user')) && getCookie(btoa('pswd'))) {
        oPhoneNum.value = atob(getCookie(btoa('user')));
        oPassword.value = atob(getCookie(btoa('pswd')));
        oRemember.checked = true;
    }
    //复选框勾选状态发生改变时，如果未勾选则清除cookie
    oRemember.onchange = function () {
        if (!this.checked) {
            delCookie(btoa('user'));
            delCookie(btoa('pswd'));
        }
    };
    //提交事件触发时，如果复选框是勾选状态则保存cookie
    $('.login-btn').click(function(){
        if (oRemember.checked) {
            setCookie(btoa('user'), btoa(oPhoneNum.value), 7); //保存帐号到cookie，有效期7天
            setCookie(btoa('pswd'), btoa(oPassword.value), 7); //保存密码到cookie，有效期7天
        }
    })
})
//设置cookie
function setCookie(name, value, day) {
    var date = new Date();
    date.setDate(date.getDate() + day);
    document.cookie = name + '=' + value + ';expires=' + date;
};
//获取cookie
function getCookie(name) {
    var reg = RegExp(name + '=([^;]+)');
    var arr = document.cookie.match(reg);
    if (arr) {
        return arr[1];
    } else {
        return '';
    }
};
//删除cookie
function delCookie(name) {
    setCookie(name, null, -1);
};