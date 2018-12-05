// http://www.jq22.com/jquery-info8371 插件地址  三级城市
var area = new LArea();
area.init({
    'trigger': '#area-li',//触发选择控件的文本框，同时选择完毕后name属性输出到该位置
    'valueTo': '#value1',//选择完毕后id属性输出到该位置
    'keys': { //绑定数据源相关字段 id对应valueTo的value属性输出 name对应trigger的value属性输出
        id: 'id',
        name: 'name'
    },
    'type': 1,//数据源类型
    'data': LAreaData//数据源
})
area.value=[1,13,1];//控制初始位置，注意：该方法并不会影响到input的value

// 上传图片 https://blog.csdn.net/scrat_kong/article/details/79230329
function uploadImg(input,img){
    var input = document.getElementById(input);
    var img = document.getElementById(img);
    var reader = new FileReader();//创建fileReader实例
    reader.readAsDataURL(input.files[0]);//发起异步请求
    reader.onload = function(){
        //读取完成后，数据保存在对象的result属性中
        img.src = this.result;
    }
}

$('.upload-input').change(function(){
    uploadImg('upload-input', 'logo-img');
})

$.ajax({
    url: 'store_league',
    type: 'POST',
    dataType: 'JSON',
    success: function(res) {
        var data = res.data;
        console.log(data);
        var leagueStr = '';
        for(var i = 0, l = data.roles.length; i < l; i++){
            if(i === 0){
                leagueStr += '<input type="radio" name="league" checked id="'+data.roles[i].id+'" class="radio-input">\
                        <label for="'+data.roles[i].id+'" class="accessory-dealer-label">'+data.roles[i].name+'</label>';
            }else{
                leagueStr += '<input type="radio" name="league" id="'+data.roles[i].id+'" class="radio-input">\
                        <label for="'+data.roles[i].id+'" class="accessory-dealer-label">'+data.roles[i].name+'</label>';
            }
        }
        $('.league-box').append(leagueStr);
        // 电话
        $('.phone').val(data.user[0].phone_num);
        // 性别
        var sex = data.user[0].sex;
        if(sex === '男'){
            $('#male')[0].checked = true;
        }else if(sex === '女'){
            $('#female')[0].checked = true;
        }
        // 真实姓名
        var realName = data.user[0].real_name;
        if(realName !== null){
            $('.name').val(realName);
        }
        // 选中服务商，经营范围显示
        $('.league-box input').change(function(){
            if($(this)[0].id == 13){
                $('.business-li').show();
            }else{
                service_setting_id = [];
                $('.business-li').hide();
            }
        })
        // 经营范围弹窗数据
        var businessRangeStr = '';
        $.each(data.service_setting_info, function(idx, val){
            businessRangeStr += '<li class="business-range-li">\
                                    <input type="checkbox" class="checkbox-input" id="range-'+val.service_setting_id+'">\
                                    <label for="range-'+val.service_setting_id+'">'+val.service_setting_name+'</label>\
                                </li>'
        })
        $('.business-range-ul').append(businessRangeStr);

        
        var service_setting_id = []; //经营范围
        // 存储 经营范围 ID
        $('.business-btn-confirm').click(function(){
            var allCheckInput = $('.business-range-ul').find('.checkbox-input');
            for(var i = 0, l = allCheckInput.length; i < l; i++){
                if(allCheckInput[i].checked){
                    service_setting_id.push(allCheckInput[i].id.split('-')[1]);
                }
            }

            $('.mask').hide();
            $('.business-pop').animate({'bottom': '-100%'}, 100);
            $('html').css('overflow', 'auto');
        })

        // 下一步按钮
        $('.next-button').click(function(){
            var store_name,
                real_name,
                phone_num,
                store_owner_seat_num = $('.seat-phone').val(),
                sex,
                store_logo_images,
                store_do_bussiness_time = $('.bussi-time').val(),  //营业时间
                store_city_address,
                store_street_address,
                store_information = $('.shop-info').val(),
                store_owner_email = $('.email').val(),
                store_owner_wechat = $('.wechat').val(),
                role_id;
            // 店铺名称
            if($('.shop-name').val() !== ''){
                store_name = $('.shop-name').val();
            }else{
                mustFill();
            }
            // 负责人姓名
            if($('.name').val() !== ''){
                real_name = $('.name').val();
            }else{
                mustFill();
            }
            // 手机号
            if($('.phone').val() !== ''){
                phone_num = $('.phone').val();
            }else{
                mustFill();
            }
            // 性别
            if($('#male')[0].checked === true){
                sex = '男';
            }else{
                sex = '女';
            }
            // 店铺logo
            if($('#upload-input')[0].files.length !== 0){
                store_logo_images = $('#upload-input')[0].files[0];
            }else{
                store_logo_images = null;
            }
            

            // 店铺所在区域
            if($('#area-li').val() !== ''){
                store_city_address = $('#area-li').val();
            }else{
                layer.open({
                    style: 'bottom:100px;',
                    type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                    skin: 'msg',
                    content: '请选择店铺所在区域',
                    time: 1.5
                })
            }
            // 店铺详细地址
            if($('.detail-addr').val() !== ''){
                store_street_address = $('.detail-addr').val();
            }else{
                mustFill();
            }

            // 我要加盟 id
            var leagueArr = $('.league-box').find('input');
            for(var i = 0, l = leagueArr.length; i < l; i++){
                if(leagueArr[i].checked){
                    role_id = leagueArr[i].id;
                }
            }
            if(store_name !== undefined && real_name !== undefined &&
            store_logo_images !== undefined && store_city_address !== undefined &&
            store_street_address !== undefined && role_id !== undefined){

                var formData = new FormData();
                formData.append('store_name', store_name);
                formData.append('real_name', real_name);
                formData.append('phone_num', phone_num);
                formData.append('store_owner_seat_num', store_owner_seat_num);
                formData.append('sex', sex);
                formData.append('store_logo_images', store_logo_images);
                formData.append('store_do_bussiness_time', store_do_bussiness_time);
                formData.append('store_city_address', store_city_address);
                formData.append('store_street_address', store_street_address);
                formData.append('store_information', store_information);
                formData.append('store_owner_email', store_owner_email);
                formData.append('store_owner_wechat', store_owner_wechat);
                formData.append('role_id', role_id);
                formData.append('service_setting_id', service_setting_id);

                // 如果选择服务商， 需要选择经营范围
                if($('.business-li').css('display') === 'flex'){//服务商
                    if(service_setting_id.length !== 0){
                        if($('.next-button').text() === '下一步'){
                            $.ajax({
                                url: 'store_add',
                                type: 'POST',
                                dataType: 'JSON',
                                processData: false,
                                contentType: false,
                                data: formData,
                                success: function(res){
                                    console.log(res);
                                    if(res.status == 1){
                                        layer.open({
                                            style: 'bottom:100px;',
                                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                                            skin: 'msg',
                                            content: res.info,
                                            time: 1.5
                                        })
                                        setTimeout(function(){
                                            location.href = 'store_verify';
                                        }, 1700)
                                    }else if(res.status == 0){
                                        layer.open({
                                            style: 'bottom:100px;',
                                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                                            skin: 'msg',
                                            content: res.info,
                                            time: 1.5
                                        })
                                    }
                                },
                                error: function(){
                                    console.log('error');
                                }
                            })
                        }else if($('.next-button').text() === '更新'){
                            $.ajax({
                                url: 'store_save',
                                type: 'POST',
                                dataType: 'JSON',
                                processData: false,
                                contentType: false,
                                data: formData,
                                success: function(res){
                                    console.log(res);
                                    if(res.status === '0'){
                                        layer.open({
                                            style: 'bottom:100px;',
                                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                                            skin: 'msg',
                                            content: res.info,
                                            time: 1.5
                                        })
                                        setTimeout(function(){
                                            location.href = 'store_verify';
                                        }, 1700)
                                    }else{
                                        layer.open({
                                            style: 'bottom:100px;',
                                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                                            skin: 'msg',
                                            content: res.info,
                                            time: 1.5
                                        })
                                        setTimeout(function(){
                                            location.href = 'store_verify';
                                        }, 1700)
                                    }
                                },
                                error: function(){
                                    console.log('error');
                                }
                            })
                        }
                    }else{
                        layer.open({
                            style: 'bottom:100px;',
                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                            skin: 'msg',
                            content: '服务商需选择服务范围',
                            time: 1.5
                        })
                    }
                }else{//配件商
                    if($('.next-button').text() === '下一步'){
                        $.ajax({
                            url: 'store_add',
                            type: 'POST',
                            dataType: 'JSON',
                            processData: false,
                            contentType: false,
                            data: formData,
                            success: function(res){
                                console.log(res);
                                if(res.status == 1){
                                    layer.open({
                                        style: 'bottom:100px;',
                                        type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                                        skin: 'msg',
                                        content: res.info,
                                        time: 1.5
                                    })
                                    setTimeout(function(){
                                        location.href = 'store_verify';
                                    }, 1700)
                                }else if(res.status == 0){
                                    layer.open({
                                        style: 'bottom:100px;',
                                        type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                                        skin: 'msg',
                                        content: res.info,
                                        time: 1.5
                                    })
                                }
                            },
                            error: function(){
                                console.log('error');
                            }
                        })
                    }else if($('.next-button').text() === '更新'){
                        $.ajax({
                            url: 'store_save',
                            type: 'POST',
                            dataType: 'JSON',
                            processData: false,
                            contentType: false,
                            data: formData,
                            success: function(res){
                                console.log(res);
                                if(res.status === '0'){
                                    layer.open({
                                        style: 'bottom:100px;',
                                        type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                                        skin: 'msg',
                                        content: res.info,
                                        time: 1.5
                                    })
                                    setTimeout(function(){
                                        location.href = 'store_verify';
                                    }, 1700)
                                }else{
                                    layer.open({
                                        style: 'bottom:100px;',
                                        type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                                        skin: 'msg',
                                        content: res.info,
                                        time: 1.5
                                    })
                                    setTimeout(function(){
                                        location.href = 'store_verify';
                                    }, 1700)
                                }
                            },
                            error: function(){
                                console.log('error');
                            }
                        })
                    }
                }
            }  
        })


        // 更新信息返回
        $.ajax({
            url: 'return_store_information',
            type: 'POST',
            dataType: 'JSON',
            success: function(res){
                console.log(res);
                if(res.status == 1){
                    var data = res.data;
                    $('.shop-name').val(data.store_name);
                    // $('.name').val()
                    $('.seat-phone').val(data.store_owner_seat_num);
                    $('#logo-img')[0].src = 'uploads/'+data.store_logo_images;
                    // 经营范围
                    if(data.service_setting_id !== null){
                        $('.business-li').show();
                        var myArr = data.service_setting_id.split(',');
                        $.each(myArr, function(idx, val){
                            $('#range-'+val+'').attr('checked', 'checked');
                        })
                    }
                    // 店铺所在区域
                    $('#area-li').val(data.store_city_address);
                    // 店铺详细地址
                    $('.detail-addr').val(data.store_street_address);
                    // 店铺信息
                    if(data.store_information !== null){
                        $('.shop-info').val(data.store_information);
                    }
                    // 邮箱
                    if(data.store_owner_email !== null){
                        $('.email').val(data.store_owner_email);
                    }
                    // 绑定微信
                    if(data.store_owner_wechat !== null){
                        $('.wechat').val(data.store_owner_wechat);
                    }
                    // 我要加盟
                    $('#'+data.role_id+'').attr('checked', 'checked');
                    $('.next-button').text('更新');
                }else if(res.status == 0){
                    $('.next-button').text('下一步');
                }
            },
            error: function(){
                console.error('error');
            }
        })

    },
    error: function(){
        console.error('error');
    }
})
// 经营范围
$('.business-li').click(function(){
    $('.mask').show();
    $('.business-pop').animate({'bottom': '0'}, 100);
    $('html').css('overflow', 'hidden');
})
$('.business-btn-cancel').click(function(){
    $('.mask').hide();
    $('.business-pop').animate({'bottom': '-100%'}, 100);
    $('html').css('overflow', 'auto');
})

function mustFill(){
    layer.open({
        style: 'bottom:100px;',
        type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
        skin: 'msg',
        content: '带星号信息需填写完整',
        time: 1.5
    })
}

