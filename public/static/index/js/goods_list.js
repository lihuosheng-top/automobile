$('.filter_box button').click(function(){
    $(this).addClass('current').siblings().removeClass('current');
    var idx = $(this).index();
})

// 获取url地址id
var url = location.search;
var id;
if(url.indexOf('?') != -1){
    id = url.substr(1).split('=')[1];
}
$.ajax({
    url: 'goods_list',
    type: 'POST',
    dataType: 'JSON',
    data: {
        'id': id
    },
    success: function(res){
        console.log(res);
        var str = '';
        $.each(res.data, function(idx, val){
            if(idx % 2 == 0){
                str += '<li>\
                            <a href="goods_detail?id='+val.id+'&&preid='+id+'">\
                                <div class="img_div">\
                                    <img src="uploads/'+val.goods_show_images+'">\
                                </div>\
                                <div class="goods_name">\
                                    <p>'+val.goods_name+'</p>\
                                </div>\
                                <div class="goods_price">\
                                    <span class="price">￥'+val.goods_adjusted_money+'</span>\
                                    <span class="pay_num">1500人购买</span>\
                                    <i class="spr icon_cart"></i>\
                                </div>\
                            </a>\
                        </li>'
            }else{
                str += '<li class="mgr0">\
                            <a href="goods_detail?id='+val.id+'&&preid='+id+'">\
                                <div class="img_div">\
                                    <img src="uploads/'+val.goods_show_images+'">\
                                </div>\
                                <div class="goods_name">\
                                    <p>'+val.goods_name+'</p>\
                                </div>\
                                <div class="goods_price">\
                                    <span class="price">￥'+val.goods_adjusted_money+'</span>\
                                    <span class="pay_num">1500人购买</span>\
                                    <i class="spr icon_cart"></i>\
                                </div>\
                            </a>\
                        </li>'
            }
        })
        $('.list_cont').append(str);
    },
    error: function(){
        console.log('error');
    }
})





















