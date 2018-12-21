// 获取url地址id
var url = location.search;
var id, preId;
if(url.indexOf('?') != -1){
    id = url.substr(1).split('&&')[0].split('=')[1];
    preId = url.substr(1).split('&&')[1].split('=')[1];
}
$('.wrapper').find('a.back').click(function(){
    location.href = 'goods_list?id=' + preId;
})
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
                            specStr += `<span class="select-on select-item">`+value+`</span>`;
                        }else{
                            specStr += `<span class="select-item">`+value+`</span>`;
                        }
                    }
                })
                specStr += '</div>';
            }
            $('.spec-wrap').prepend(specStr);
            // 安装方式
            var installationArr = val.goods_delivery.split(',');
            var installationStr = '';
            for(var j = 0; j < installationArr.length; j++){
                if(j === 0){
                    installationStr += `<button class="select-on select-item btn-item">`+installationArr[j]+`</button>`;
                }else{
                    installationStr += `<button class="select-item btn-item">`+installationArr[j]+`</button>`;
                }
            }
            $('.installation').append(installationStr);
            // 立即购买 身上放商品id
            $('.select-buy').prop('id', val.id);
            // 选择切换class
            $('.spec-wrap').on('click', '.select-item', function(){
                $(this).addClass('select-on');
                $(this).siblings('.select-item').removeClass('select-on');
                // if($(this).hasClass('btn-item')){
                //     if($(this)[0].innerText === '无需安装'){
                //         $('.select-shop').hide();
                //     }else{
                //         $('.select-shop').show();
                //     }
                // }
                var selectSpec = '';
                $.each($('.select-on'), function(idx, val){
                    selectSpec += $(val).text() + ' ';
                })
                $('.select-goods-spec').text('规格：' + selectSpec);
            })
        })
        swiperEvent();
    },
    error: function(){
        console.log('error');
    }
})

// swiper初始化
function swiperEvent(){
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
}

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

// 所有评论切换
$('.comment_classify_box li').click(function(){
    $(this).addClass('active').siblings().removeClass('active');
})

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
    $('.select-calculator_val').val($('.calculator_val').val());
    
    // 点开弹窗 规格显示 默认选中的几个
    var selectSpec = '';
    $.each($('.select-on'), function(idx, val){
        selectSpec += $(val).text() + ' ';
    })
    $('.select-goods-spec').text('规格：' + selectSpec);
})

// 立即购买 弹窗
$('.select-buy').click(function(){
    // 地址返回
    var $this = this;
    $.ajax({
        url: 'member_default_address_return',
        type: 'POST',
        dataType: 'JSON',
        success: function(res){
            console.log(res);
            // if(res.status == 0){
            //     layer.open({
            //         content: res.info,
            //         btn: ['确定', '取消'],
            //         yes: function (index) {
            //             layer.close(index);
            //             location.href = 'member_address_add?id=271&&preid=10';
            //         }
            //     });
            // }else if(res.status == 2){
            //     layer.open({
            //         content: res.info,
            //         btn: ['确定', '取消'],
            //         yes: function (index) {
            //             layer.close(index);
            //             location.href = 'login';
            //         }
            //     });
            // }else{
            //     if($('.select-goods-spec').text() !== '选择规格'){
            //         var goods_id = $($this)[0].id;
            //         var goods_number = $('.select-calculator_val').val();
            //         var goods_standard = $('.select-goods-spec').text();
            //         $.ajax({
            //             url: 'get_goods_id_save',
            //             type: 'POST',
            //             dataType: 'JSON',
            //             data: {
            //                 'goods_id': goods_id,
            //                 'goods_number': goods_number,
            //                 'goods_standard': goods_standard,
            //             },
            //             success: function(res){
            //                 console.log(res);
            //                 location.href = 'ios_api_order_parts_firm_order?id=' + id + '&&preid=' + preId;
            //             },
            //             error: function(){
            //                 console.log('error')
            //             }
            //         })
            //     }else{
            //         layer.open({
            //             skin: 'msg',
            //             content: '请选择规格',
            //             time: 1
            //         })
            //     }
            // }
        },
        error: function(){
            console.log('error');
        }
    })
})
// 购买弹窗  加入购物车
$('.select-add-cart').click(function(){
    $('.select-ser-pop').removeClass('select-ser-easeout');
    $('.mask').hide();
    layer.open({
        skin: 'msg',
        content: '加入购物车成功',
        time: 1
    })
    
})
// 加入购物车
$('.add-cart').click(function(){
    layer.open({
        skin: 'msg',
        content: '加入购物车成功',
        time: 1
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

// 收藏
$.ajax({
    url: 'show_collection',
    type: 'POST',
    dataType: 'JSON',
    data: {
        'id': id
    },
    success: function(res){
        console.log(res);
        if(res.status == 1){
            $('.collect').addClass('collect-on');
        }
    },
    error: function(){
        console.log('error')
    }
})
$('.collect').click(function(){
    $(this).toggleClass('collect-on');
    if($(this).hasClass('collect-on')){
        $.ajax({
            url: 'collection_add',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'id': id
            },
            success: function(res){
                console.log(res);
                layer.open({
                    skin: 'msg',
                    content: '收藏成功',
                    time: 1
                })
            },
            error: function(){
                console.log('error')
            }
        })
    }else{
        $.ajax({
            url: 'collection_del',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'id': id
            },
            success: function(res){
                console.log(res);
                layer.open({
                    skin: 'msg',
                    content: '取消收藏',
                    time: 1
                })
            },
            error: function(){
                console.log('error')
            }
        })
    }
})

// 判断有没有登录
function isLogin(){
    $.ajax({
        url: 'isLogin',
        type: 'POST',
        dataType: 'JSON',
        success: function(res){
            console.log(res);
        },
        error: function(){
            console.log('error');
        }
    })
}
