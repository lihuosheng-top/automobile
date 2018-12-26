// 初始化star
var starArr = [];
function initStar(){
    layui.use(['rate'], function(){
        var rate = layui.rate;
        layui.each($('.star'), function(idx, elem){
            starArr[idx] = 5;
            rate.render({
                elem: elem,
                value: 5,
                text: true,
                setText: function(val){
                    var arrs = {
                        '1': '很差',
                        '2': '差',
                        '3': '一般',
                        '4': '好',
                        '5': '很好',
                    };
                    this.span.text(arrs[val]);
                },
                choose: (value) => {
                    starArr[idx] = value;
                    console.log(starArr);
                }
            })
        })
    })
}
// 获取商品信息
$.ajax({
    url: 'evaluate_index',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        var str = '';
        $.each(res.data, function(idx, val){
            str += `<div class="evaluation-subject" id="`+val.id+`">
                        <div class="goods-evaluate">
                            <div class="goods-img">
                                <img src="uploads/`+val.goods_image+`">
                            </div>
                            <p class="ms">描述相符</p>
                            <div class="star"></div>
                        </div>
                        <div class="evaluate-txt">
                            <textarea placeholder="宝贝满足你的期待吗？说说它的优点和美中不足的地方吧"></textarea>
                        </div>
                        <div class="vi-camera">
                            <span class="switch-span">添加图片
                                <input type="file" multiple id="upload-`+idx+`">
                            </span>
                        </div>
                    </div>`
        })
        $('.header').after(str);
        // 初始化star
        initStar();
        // 上传图片
        $('.switch-span').on('change', 'input', function(){
            var inputElem = $(this)[0];
            var switchSpan = $(this).parent();
            var id = $(this).parents('.evaluation-subject').attr('id');
            changeEvent(inputElem, switchSpan, id);
            // console.log(filesArr);
            console.log(filesObj)
        })
    },
    error: function(){
        console.log('error');
    }
})
var filesObj = {};
function changeEvent(inputElem, clickObj, id){
    var imgArrDom = [];
    var str = '';
    var len =  inputElem.files.length;
    // // 限制上传图片
    var uploadItemLen = $(clickObj).siblings('.upload-item').length;
    if(uploadItemLen + len <= 4){
        // 第一次实例化数组
        if(filesObj[id] === undefined){
            filesObj[id] = new Array();
        }
        for(var i = 0; i < len; i++){
            // 存图片地址
            imgArrDom.push(getObjectURL(inputElem.files[i]));
            // 把files用id区分开插入
            filesObj[id].push(inputElem.files[i]);
        }
        $.each(imgArrDom, function(idx, val){
            str += `<div class="upload-item">
                        <img src="`+val+`">
                        <button class="del-img">×</button>
                    </div>`
        })
        $(clickObj).before(str);
    }else{
        layer.open({
            skin: 'msg',
            content: '最多上传4张图片',
            time: 1
        })
    }
    // 删除图片
    $('.upload-item').on('click', '.del-img', function(){
        var $index = $('.del-img').index($(this));
        filesObj[id].splice($index, 1);
        console.log(filesObj)
        $(this).parent().remove();
    })
}
// 在浏览器上预览本地图片
function getObjectURL(file) {
    var url = null;
    if(window.URL != undefined) { // mozilla(firefox)
        url = window.URL.createObjectURL(file);
    } else if(window.webkitURL != undefined) { // webkit or chrome
        url = window.webkitURL.createObjectURL(file);
    }
    return url;
}
// 是否准时
var isOnTime = 1;
$('.on-time').on('change', 'input', function(){
    var onTimeText = $(this).attr('id');
    if(onTimeText === 'late'){
        isOnTime = -1;
    }else{
        isOnTime = 1;
    }
    console.log(isOnTime);
})


// 发布
var formData = new FormData();
$('.publish-btn').click(function(){
    var orderId = [];
    var evaluationSubjectArr = $('.evaluation-subject');
    // 商品id
    $.each(evaluationSubjectArr, function(idx, val){
        orderId.push(val.id);
    })
    // 用户评价内容
    var evaluateContent = [];
    $.each($('textarea'), function(idx, val){
        evaluateContent.push(val.value);
    })
    var idArr = [];
    $.each($('.evaluation-subject'), function(idx, val){
        idArr.push(val.id);
    })
    $.each(idArr, function(idx, val){
        // console.log(filesObj[val])
        $.each(filesObj[val], function(index, value){
            console.log(value);
            formData.append('filesArr['+val+']', value);
        })
    })
    $.each(orderId, function(idx, val){
        formData.append('orderId[]', val);
    })
    $.each(evaluateContent, function(idx, val){
        formData.append('evaluateContent[]', val);
    })
    $.each(starArr, function(idx, val){
        formData.append('starArr[]', val);
    })
    formData.append('isOnTime', isOnTime);

    $.ajax({
        url: 'evaluate_parts_add',
        type: 'POST',
        dataType: 'formData',
        processData: false,
        contentType: false,
        data: formData,
        success: function(res){
            console.log(res);
        },
        error: function(){
            console.log('error');
        }
    })
})