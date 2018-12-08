// 获取url地址id
var url = location.search;
var id;
if(url.indexOf('?') != -1){
    id = url.substr(1).split('=')[1];
}
$.ajax({
    url: 'goods_detail',
    type: 'POST',
    dataType: 'JSON',
    data: {
        'id': id
    },
    success: function(res){
        console.log(res);
        // 轮播图
        var swiperImgStr = '';
        $.each(res.data[0].images, function(idx, val){
            swiperImgStr += '<div class="swiper-slide">\
                                <img src="uploads/'+val.goods_images+'">\
                            </div>'
        })
        $('.swiper-wrapper').append(swiperImgStr);
        $.each(res.data, function(idx, val){
            // 商品名字
            $('.goods_cont .goods_name').html(val.goods_name);
            // 卖点
            $('.selling-point').html(val.goods_selling);
            // 划线价
            $('.through').html('￥' + val.goods_bottom_money);
            // 售价
            $('.sale').html('￥' + val.goods_adjusted_money);
            // 库存
            $('.stock').html('库存' + val.goods_repertory + '件');
            // 商品详情
            $('.detail-img-box').html(val.goods_text);
            // 专用参数
            $('.parameter-brand').text(val.dedicated_vehicle);
            $('.parameter-series').text(val.goods_car_series.split(',').join('  '));
            $('.parameter-year').text(val.goods_car_year.split(',').join('  '));
            $('.parameter-displacement').text(val.goods_car_displacement.split(',').join('  '));
            // 选择服务弹窗
            $('.select-goods-img img')[0].src = 'uploads/' + val.goods_show_images;
            $('.select-goods-price').text('￥' + val.goods_adjusted_money);
            $('.select-goods-stock').text('库存' + val.goods_repertory + '件');
            var specStr =  '';
            for(var i = 0, l = val.goods_standard_name.length; i < l; i++){
                specStr += `<div class="spec-box">
                            <p>`+val.goods_standard_name[i]+`</p>`;
                // 遍历属性值
                $.each(val.goods_standard_value[i], function(index, value){
                    // 去掉空值
                    if(value !== ''){
                        if(index === 0){
                            specStr += `<span class="select-on">`+value+`</span>`;
                        }else{
                            specStr += `<span>`+value+`</span>`;
                        }
                    }
                })
                specStr += '</div>';
            }
            $('.spec-wrap').prepend(specStr);
            // 选择切换class
            $('.spec-wrap').on('click', 'span', function(){
                $(this).addClass('select-on');
                $(this).siblings('span').removeClass('select-on');
                if($(this)[0].innerText === '无需安装'){
                    $('.select-shop').hide();
                }else{
                    $('.select-shop').show();
                }
                var selectSpec = '';
                $.each($('.select-on'), function(idx, val){
                    selectSpec += $(val).text() + ' ';
                })
                $('.select-goods-spec').text('规格：' + selectSpec);
            })
        })
        
        // swiper初始化
        var mySwiper = new Swiper ('.swiper-container', {
            direction: 'horizontal', // 垂直切换选项
            loop: true, // 循环模式选项
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,//用户操作swiper后，不禁止autoplay
            },
            // 如果需要分页器
            pagination: {
                el: '.swiper-pagination',
            },
        }) 

    },
    error: function(){
        console.log('error');
    }
})



// 产品参数
$('.product-parameter').click(function(){
    $('.mask').show();
    $('.product-parameter-pop').animate({'bottom': '0'});
    $('html').css('overflow', 'hidden');
})
$('.parameter-btn').click(function(){
    $('.mask').hide();
    $('.product-parameter-pop').animate({'bottom': '-100%'});
    $('html').css('overflow', 'auto');
})





// 显示 隐藏 评价弹窗 
function showPop(){
    $('.pop').css('transform', 'translateX(0)');
    $('html').css('overflow', 'hidden');
}
function hidePop(){
    $('.pop').css('transform', 'translateX(100%)');
    $('html').css('overflow', 'auto');
}
// 往下滑 头部添加背景
// $(window).on('scroll', function(){
//     var scrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop;
//     if(scrollTop > 0){
//         $('.wrapper .head').css('background', 'rgba(255, 255, 255, .5)');
//     }else{
//         $('.wrapper .head').css('background', 'transparent');
//     }
// })

// 所有评论切换
$('.comment_classify_box li').click(function(){
    $(this).addClass('active').siblings().removeClass('active');
})

// 图片展示 start
var index, curr_container, curr_img_len;
$(function(){
    $('.comment_img_ul comment_img_li').click(function(e){
        index = $(this).index();// 当前被点击的索引值
        curr_container = e.liveFired;//被点击li的父级
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
$('.img_box').click(function(e){
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

// 商品详情， 评价切换
$('.switch-detail').click(function(){
    $(this).addClass('switch-on').siblings().removeClass('switch-on');
    $('.detail-box').show();
    $('.comment_box').hide();
})

$('.switch-comment').click(function(){
    $(this).addClass('switch-on').siblings().removeClass('switch-on');
    $('.comment_box').show();
    $('.detail-box').hide();
})

// 查看评价详情
$('.comment_text_a').click(function(){
    $('.wrapper').hide();
    $('.comment-detail-pop').show();
})
$('.detail-back').click(function(){
    $('.wrapper').show();
    $('.comment-detail-pop').hide();
})

// 选择服务
$('.ser-type').add('#buy').click(function(){
    $('html').css('overflow','hidden');
    $('.mask').show();
    $('.select-ser-pop').addClass('select-ser-easeout');
})
// 立即购买 弹窗
$('.select-buy').click(function(){
    if($('.select-goods-spec').text() !== '选择规格'){
        location.href = 'ios_api_order_parts_firm_order?id=' + id;
    }else{
        layer.open({
            skin: 'msg',
            content: '请选择规格',
            time: 1.5
        })
    }
})
// 购买弹窗  加入购物车
$('.select-add-cart').click(function(){
    $('.select-ser-pop').removeClass('select-ser-easeout');
    $('.mask').hide();
    layer.open({
        skin: 'msg',
        content: '加入购物车成功',
        time: 1.5
    })
    
})
// 加入购物车
$('.add-cart').click(function(){
    layer.open({
        style: 'bottom:100px;',
        type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
        skin: 'msg',
        content: '加入购物车成功',
        time: 1.5
    })
})
// 关闭选择服务 弹窗
$('.close-select-pop').click(function(){
    $('html').css('overflow','auto');
    $('.mask').hide();
    $('.select-ser-pop').removeClass('select-ser-easeout');
})

// 购买数量
$(function(){
    // 减
    var calculator_val = document.getElementsByClassName('calculator_val')[0];
    $('.minus').click(function(){
        if(calculator_val.value > 1){
            calculator_val.value -= 1;
        }else{
            calculator_val.value = 1;
        }
    })
    // 加
    $('.increase').click(function(){
        var add =  calculator_val.value - 0;
        add += 1;
        calculator_val.value = add;
    })
})

// 弹窗 购买数量
$(function(){
    // 减
    var select_calculator_val = document.getElementsByClassName('select-calculator_val')[0];
    $('.select-minus').click(function(){
        if(select_calculator_val.value > 1){
            select_calculator_val.value -= 1;
        }else{
            select_calculator_val.value = 1;
        }
    })
    // 加
    $('.select-increase').click(function(){
        var add =  select_calculator_val.value - 0;
        add += 1;
        select_calculator_val.value = add;
    })
})

