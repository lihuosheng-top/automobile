$('.filter_box button').click(function(){
    $(this).addClass('current').siblings().removeClass('current');
    var idx = $(this).index();
})

// 获取url地址id
var url = location.search;
var brandid;
if(url.indexOf('?') != -1){
    brandid = url.substr(1).split('=')[1];
    layDataAjax('goods_list', brandid);
}else{
    $('#search').focus();

    var timer = null;
    var brandid = '';
    $('#search').on('input', function(){
        var _self = this;
        clearTimeout(timer);
        timer = setTimeout(function(){
            searchAjax('goods_list_search', brandid, _self.value);
        }, 500);
    })
}
function layDataAjax(url, brandid){
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': brandid
        },
        success: function(res){
            console.log(res);
            var str = '';
            $.each(res.data, function(idx, val){
                if(idx % 2 == 0){
                    str += '<li>\
                                <a href="goods_detail?id='+val.id+'&preid='+brandid+'">\
                                    <div class="img_div">\
                                        <img src="uploads/'+val.goods_show_images+'">\
                                    </div>\
                                    <div class="goods_name">\
                                        <p class="txt-hid-two">'+val.goods_name+'</p>\
                                    </div>\
                                    <div class="goods_price">\
                                        <span class="price">￥'+val.special[0].goods_adjusted_price+'</span>\
                                        <span class="pay_num">'+val.statistical_quantity+'人购买</span>\
                                    </div>\
                                </a>\
                            </li>'
                    return;
                }
                str += '<li class="mgr0">\
                            <a href="goods_detail?id='+val.id+'&preid='+brandid+'">\
                                <div class="img_div">\
                                    <img src="uploads/'+val.goods_show_images+'">\
                                </div>\
                                <div class="goods_name">\
                                    <p class="txt-hid-two">'+val.goods_name+'</p>\
                                </div>\
                                <div class="goods_price">\
                                    <span class="price">￥'+val.special[0].goods_adjusted_price+'</span>\
                                    <span class="pay_num">'+val.statistical_quantity+'人购买</span>\
                                </div>\
                            </a>\
                        </li>'
                
            })
            $('.list_cont').html(str);
        },
        error: function(){
            console.log('error');
        }
    })
}
var timer = null;
$('#search').on('input', function(){
    var _self = this;
    clearTimeout(timer);
    timer = setTimeout(function(){
        searchAjax('goods_list_search', brandid, _self.value);
    }, 500);
})
function searchAjax(url, brandid, searchTxt){
    console.log(arguments)
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': brandid,
            'goods_name': searchTxt
        },
        success: function(res){
            console.log(res);
            var str = '';
            $.each(res.data, function(idx, val){
                if(idx % 2 == 0){
                    str += '<li>\
                                <a href="goods_detail?id='+val.id+'&preid='+brandid+'">\
                                    <div class="img_div">\
                                        <img src="uploads/'+val.goods_show_images+'">\
                                    </div>\
                                    <div class="goods_name">\
                                        <p class="txt-hid-two">'+val.goods_name+'</p>\
                                    </div>\
                                    <div class="goods_price">\
                                        <span class="price">￥'+val.special[0].goods_adjusted_price+'</span>\
                                        <span class="pay_num">'+val.statistical_quantity+'人购买</span>\
                                    </div>\
                                </a>\
                            </li>'
                    return;
                }
                str += '<li class="mgr0">\
                            <a href="goods_detail?id='+val.id+'&preid='+brandid+'">\
                                <div class="img_div">\
                                    <img src="uploads/'+val.goods_show_images+'">\
                                </div>\
                                <div class="goods_name">\
                                    <p class="txt-hid-two">'+val.goods_name+'</p>\
                                </div>\
                                <div class="goods_price">\
                                    <span class="price">￥'+val.special[0].goods_adjusted_price+'</span>\
                                    <span class="pay_num">'+val.statistical_quantity+'人购买</span>\
                                </div>\
                            </a>\
                        </li>'
                
            })
            $('.list_cont').html('').html(str);
        },
        error: function(){
            console.log('error');
        }
    })
}




















