
// 初始化star
function initStar(){
    layui.use(['rate'], function(){
        var rate = layui.rate;
        layui.each($('.star'), function(idx, elem){
            rate.render({
                elem: elem,
                value: 3,
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
initStar();

// 获取商品信息
$.ajax({
    url: 'evaluate_index',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        var str = '';
        $.each(res.data, function(idx, val){
            str += `<div class="evaluation-subject">
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
                            <div class="upload-item">
                                <img src="__STATIC__/index/image/15.jpg">
                                <button class="del-img">×</button>
                            </div>
                            <span class="switch-span">添加图片</span>
                            <input type="file" multiple hidden id="upload">
                        </div>
                    </div>`
        })
        // $('.header').after(str);
    },
    error: function(){
        console.log('error');
    }
})

// 上传图片
var imagesFileArray = [];
$('.switch-span').click(function(){
    $('#upload').click();
})


