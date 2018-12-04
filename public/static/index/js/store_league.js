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
                $('.business-li').hide();
            }
        })
        // 经营范围弹窗
        var businessRangeStr = '';
        $.each(data.service_setting_info, function(idx, val){
            businessRangeStr += '<li class="business-range-li">\
                                    <input type="checkbox" class="checkbox-input" id="range-'+val.service_setting_id+'">\
                                    <label for="range-'+val.service_setting_id+'">'+val.service_setting_name+'</label>\
                                </li>'
        })
        $('.business-range-ul').append(businessRangeStr);

        // 下一步
        $('.next-button').click(function(){
            $.ajax({
                url: 'store_add',
                type: 'POST',
                dataType: 'JSON',
                data: {

                },
                success: function(res){
                    console.log(res);
                },
                error: function(){
                    console.error('error');
                }
            })
        })
    },
    error: function(){
        console.error('error');
    }
})

$('.business-li').click(function(){
    $('.mask').show();
    $('.business-pop').animate({'bottom': '0'});
    $('html').css('overflow', 'hidden');
})
