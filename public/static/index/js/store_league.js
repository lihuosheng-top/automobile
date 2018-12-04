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
        // var leagueStr = '';
        // for(var i = 0, l = data.roles.length; i < l; i++){
        //     leagueStr += '<input type="radio" name="league" id="'+data.roles[i].id+'" checked class="radio-input">\
        //                 <label for="'+data.roles[i].id+'" class="accessory-dealer-label">'+data.roles[i].name+'</label>';
        // }
        // $('.league-box').append(leagueStr);
    },
    error: function(){
        console.error('error');
    }
})