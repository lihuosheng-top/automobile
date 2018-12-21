
// 全部弹窗
function showPop(){
    $('.pop').css('transform', 'translateX(0)');
    $('html').css('overflow', 'hidden');
}
function hidePop(){
    $('.pop').css('transform', 'translateX(100%)');
    $('html').css('overflow', 'auto');
}
$('.filter-com-ul').on('click', 'li', function(){
    $(this).siblings().removeClass('filter-li-this');
    $(this).toggleClass('filter-li-this');
})
$('.filter-service-ul').on('click', 'li', function(){
    $(this).siblings().removeClass('filter-service-li');
    $(this).toggleClass('filter-service-li');
})

// 往下滑 头部添加背景
$(window).on('scroll', function(){
    var scrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop;
    if(scrollTop > 0){
        $('.wrapper .head').css('background', 'rgba(255, 255, 255, .5)');
    }else{
        $('.wrapper .head').css('background', 'transparent');
    }
})

// 所有评论切换
$('.comment_classify_box li').on('click', function(){
    $(this).addClass('active').siblings().removeClass('active');
})

// 图片展示 start
var index, curr_container, curr_img_len;
$(function(){
    $('.comment_img_ul .comment_img_li').on('click', function(e){
        index = $(this).index();// 当前被点击的索引值
        curr_container = $(this).parent();//被点击li的父级
        curr_img_len = $(curr_container).children().length;//被点击容器中li的length
        $('.show_img .img_num').html(index+1+'/'+curr_img_len);//显示图片数量
        loadImage(e.target);//执行 传参
    })
})
// 屏幕比例
var proportion = $(window).height() / $(window).width();
// 放大图片
function loadImage(obj){
    $('.show_img').show();// 显示容器
    $('.img_box').empty();
    $('html').css('overflow', 'hidden');
    var oImg = new Image();// 实例化img
        oImg.src =  obj.src;//  给新建img 赋值src
    oImg.onload = function(){//图片加载完显示
        // 计算图片宽高比
        var img_h = this.height,
            img_w = this.width;
        if(img_h / img_w > proportion){// 图片的高度宽度比例大于 屏幕比例  图片高度100%
            $(oImg).appendTo($('.img_box')).css('height', '100%');
        }else{// 图片的高度宽度比例小于 屏幕比例  图片宽度100%
            $(oImg).appendTo($('.img_box')).css('width', '100%');
        }
    }
}
$('.img_box').on('click', function(e){
    e.cancelBubble = true;
    if(!$(e.targeet).hasClass('.img_box')){
        $('.show_img').hide();
    }
    if($('.pop').css('transform') != 'translateX(0px)'){
        $('html').css('overflow', 'auto');
    }

}).on('swipeLeft', function(){//用户左划
    index++;
    if(index > curr_img_len - 1){//如果index大于总长度 显示最后一张
        index = curr_img_len - 1;
    }else{
        //  在父级容器中 选中对应index 中的img元素
        var nextImg = $(curr_container).find('img').eq(index)[0];
        $('.show_img .img_num').html(index+1+'/'+curr_img_len);
        loadImage(nextImg);
    }
}).on('swipeRight', function(){//用户右划
    console.log(111)
    index--;
    if(index < 0){
        index = 0;
    }else{
        var preImg = $(curr_container).find('img').eq(index)[0];
        $('.show_img .img_num').html(index+1+'/'+curr_img_len);
        loadImage(preImg);
    }
})
// 图片展示 end

// 获取url地址id
var url = location.search;
var storeId, serviceSettingId;
var urlLen = url.substr(1).split('&').length;

if(url.indexOf('?') != -1){
    storeId = url.substr(1).split('&')[0].split('=')[1];
    serviceSettingId = url.substr(1).split('&')[0].split('=')[1];
}
if(urlLen > 1){
    // 选择服务类型进来
    $.ajax({
        url: 'reservation_detail',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': storeId,
            'service_setting_id': serviceSettingId
        },
        success: function(data){
            console.log(data);
            if(data.status == 1){
                // 商品
                var str = '';
                $.each(data.data.goods, function(idx, val){
                    str += `<div class="goods-colla-item">
                                <div class="goods-img-box">
                                    <img src="uploads/`+val.goods_show_images+`">
                                </div>
                                <div class="goods-info-box">
                                    <p class="goods-name txt-hid-two">`+val.goods_name+`</p>
                                    <p class="goods-selling txt-hid-two">`+val.goods_describe+`</p>
                                    <p class="goods-price">￥`+val.goods_adjusted_money+`</p>
                                </div>
                            </div>`
                })
                $('.goods-content').prepend(str);
                // 服务项目
                var str2 = '';
                $.each(data.data.store, function(idx, val){
                    $('.shop_name').text(val.store_name);
                    $('.addr_p span').text(val.store_detailed_address);
                    $('.shop-describe').text(val.store_information);

                    str2 += `<div class="service-colla-item">
                                <div class="service-colla-title">
                                    <p class="service-subtitle">`+val.serve_name+`</p>
                                    <p class="service-money"></p>
                                    <i class="spr icon-uncheck"></i>
                                </div>
                                <div class="service-colla-content" style="display:none;">
                                    <ul>`
                    $.each(val.goods, function(idx, val){
                        if(val.service_money === null && val.ruling_money === null){
                            str2 += `<li>
                                        <p class="service-car-type">`+val.vehicle_model+`</p>
                                        <div class="content-money-div">
                                            <p class="sale"><span>面议</span></p>
                                        </div>
                                        <i class="spr icon-uncheck"></i>
                                    </li>`
                        }else if(val.service_money !== null && val.ruling_money === null){
                            str2 += `<li>
                                        <p class="service-car-type">`+val.vehicle_model+`</p>
                                        <div class="content-money-div">
                                            <p class="sale">￥<span>`+val.service_money+`</span></p>
                                        </div>
                                        <i class="spr icon-uncheck"></i>
                                    </li>`
                        }else if(val.service_money !== null && val.ruling_money !== null){
                            str2 += `<li>
                                        <p class="service-car-type">`+val.vehicle_model+`</p>
                                        <div class="content-money-div">
                                            <p class="sale">￥<span>`+val.service_money+`</span></p>
                                            <p class="thro">￥<span>`+val.ruling_money+`</span></p>
                                        </div>
                                        <i class="spr icon-uncheck"></i>
                                    </li>`
                        }
                        str2 += `</ul>
                            </div>
                        </div>`
                    })
                })
                $('.service-content').append(str2);
                selectEvent();
            }
        },
        error: function(){
            console.log('error');
        }
    })
}else{
    // 首页热门店铺进来
    $.ajax({
        url: 'index_shop_goods',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': storeId
        },
        success: function(res){
            console.log('获取店铺商品',res);
            if(res.status == 1){
                // 商品
                var str = '';
                $.each(res.data.goods, function(idx, val){
                    str += `<div class="goods-colla-item">
                                <div class="goods-img-box">
                                    <img src="uploads/`+val.goods_show_images+`">
                                </div>
                                <div class="goods-info-box">
                                    <p class="goods-name txt-hid-two">`+val.goods_name+`</p>
                                    <p class="goods-selling txt-hid-two">`+val.goods_describe+`</p>
                                    <p class="goods-price">￥`+val.goods_adjusted_money+`</p>
                                </div>
                            </div>`
                })
                $('.goods-content').prepend(str);
                // 服务项目
                var str2 = '';
                $.each(res.data.serve_data, function(idx, val){
                    str2 += `<div class="service-colla-item">
                                <div class="service-colla-title">
                                    <p class="service-subtitle">`+val.serve_name+`</p>
                                    <p class="service-money"></p>
                                    <i class="spr icon-uncheck"></i>
                                </div>
                                <div class="service-colla-content" style="display:none;">
                                    <ul>`
                    $.each(val.serve_goods, function(idx, val){
                        if(val.service_money === null && val.ruling_money === null){
                            str2 += `<li>
                                        <p class="service-car-type">`+val.vehicle_model+`</p>
                                        <div class="content-money-div">
                                            <p class="sale"><span>面议</span></p>
                                        </div>
                                        <i class="spr icon-uncheck"></i>
                                    </li>`
                        }else if(val.service_money !== null && val.ruling_money === null){
                            str2 += `<li>
                                        <p class="service-car-type">`+val.vehicle_model+`</p>
                                        <div class="content-money-div">
                                            <p class="sale">￥<span>`+val.service_money+`</span></p>
                                        </div>
                                        <i class="spr icon-uncheck"></i>
                                    </li>`
                        }else if(val.service_money !== null && val.ruling_money !== null){
                            str2 += `<li>
                                        <p class="service-car-type">`+val.vehicle_model+`</p>
                                        <div class="content-money-div">
                                            <p class="sale">￥<span>`+val.service_money+`</span></p>
                                            <p class="thro">￥<span>`+val.ruling_money+`</span></p>
                                        </div>
                                        <i class="spr icon-uncheck"></i>
                                    </li>`
                        }
                    })
                    str2 += `</ul>
                        </div>
                    </div>`              
                })
                $('.service-content').append(str2);
                selectEvent();
            }
        },
        error: function(){
            console.log('error');
        }
    })

}

function selectEvent(){
    // 选择
    $('.service-colla-title').click(function(e){
        e.preventDefault();
        $(this).find('.icon-uncheck').toggleClass('icon-check');
        if($(this).find('.icon-uncheck').hasClass('icon-check')){
            $(this).siblings('.service-colla-content').show();
            // 合并打开的service-colla-content
            $(this).parent().siblings().find('.service-colla-content').hide();
            $(this).parent().siblings().find('.icon-uncheck').removeClass('icon-check');
        }else{
            $(this).siblings('.service-colla-content').hide();
        }
    })
    $('.service-colla-content li').click(function(){
        $(this).find('.icon-uncheck').toggleClass('icon-check');
        if($(this).find('.icon-uncheck').hasClass('icon-check')){
            $(this).siblings().find('.icon-uncheck').removeClass('icon-check');
            $('.bespeak-btn').removeAttr('disabled')
        }else{
            $('.bespeak-btn').prop('disabled', 'disabled');
        }
    })
}

// 切换服务项目 本店商品
$('.service-tab-title').on('click', 'li', function(){
    $(this).siblings().removeClass('service-this');
    $(this).addClass('service-this');
    var index = $(this).index();
    switch(index){
        case 0:
            $('.service-content').show();
            $('.goods-content').hide();
            break;
        case 1:
            $('.service-content').hide();
            $('.goods-content').show();
            break;
    }
})

// 筛选评论
$('.filter-ul').on('click', 'li', function(){
    $(this).siblings().removeClass('filter-this');
    $(this).addClass('filter-this');
})

// 确定预约
$('.bespeak-btn').click(function(){
    location.href = 'reservation_info';
})