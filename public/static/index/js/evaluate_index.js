
// 初始化star
function initStar(){
    layui.use(['rate'], function(){
        var rate = layui.rate;
        layui.each($('.star'), function(idx, elem){
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
        $('.switch-span').on('click', 'input', function(){
            var inputElem = $(this).attr('id');
            var switchSpan = $(this).parent();
            changeEvent(inputElem, switchSpan);
        })
    },
    error: function(){
        console.log('error');
    }
})
function changeEvent(inputElem, clickObj){
    $('#'+inputElem).change(function(){
        var imgArrDom = [];
        var str = '';
        var len =  this.files.length;
        console.log(this.files)
        // 限制上传图片
        var uploadItemLen = $(clickObj).siblings('.upload-item').length;
        if(uploadItemLen + len <= 4){
            for(var i = 0; i < len; i++){
                // 存图片地址
                imgArrDom.push(getObjectURL(this.files[i]));
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
                style: 'bottom:100px;',
                type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                skin: 'msg',
                content: '最多上传4张图片',
                time: 2
            })
        }
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

// 发布
$('.publish-btn').click(function(){
    var orderId = [];
    var evaluationSubjectArr = $('.evaluation-subject');
    $.each(evaluationSubjectArr, function(idx, val){
        orderId.push(val.id);
    })
    $.ajax({
        url: 'evaluate_parts_add',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'order_id': orderId,
        },
        success: function(res){
            console.log(res);
        },
        error: function(){
            console.log('error');
        }
    })
})
