// 获取url地址id
var url = location.search;
// preStoreId 从店铺商品进来的详情
var id, preId, specId, store_id, preStoreId, hotStatus, settingid;
if(url.indexOf('?') != -1){
    id = url.substr(1).split('&')[0].split('=')[1];
    preId = url.substr(1).split('&')[1].split('=')[1];
    var urlLen = url.substr(1).split('&').length;
    if(urlLen == 3){
        preStoreId = url.substr(1).split('&')[2].split('=')[1];
    }else if(urlLen == 4){
        hotStatus = 1;
    }else if(urlLen == 5){
        settingid = url.substr(1).split('&')[4].split('=')[1];
    }
    console.log(hotStatus);
}
$('.wrapper').find('a.back').click(function(){
    if(preStoreId != undefined){
        location.href = 'store_index?storeId=' + preStoreId;
    }else if(hotStatus != undefined){
        location.href = 'reservation_detail?store_id=' + store_id;
    }else if(settingid != undefined){
        location.href = 'reservation_detail?store_id=' + store_id+'&service_setting_id='+settingid;
    }else{
        location.href = 'goods_list?id=' + preId;
    }
})
$.ajax({
    url: 'goods_detail',
    type: 'POST',
    dataType: 'JSON',
    data: {
        'id': id
    },
    success: function(res){
        console.log('商品信息', res);
        // 轮播图
        var swiperImgStr = '';
        $.each(res.data[0].images, function(idx, val){
            swiperImgStr += '<div class="swiper-slide">\
                                <img src="uploads/'+val.goods_images+'">\
                            </div>'
        })
        $('.swiper-wrapper').append(swiperImgStr);
        $.each(res.data, function(idx, val){
            // 店铺id
            store_id = val.store_id;
            // 商品名字
            $('.goods_cont .goods_name .name').text(val.goods_name);
            // 热门
            $('.goods_cont .goods_name .hot').text(val.goods_sign);
            // 卖点
            $('.selling-point').html(val.goods_selling);
            // 划线价
            $('.through').html('￥' + val.goods_standard[0].cost);
            // 售价
            $('.sale').html('￥' + val.goods_standard[0].goods_adjusted_price);
            // 库存
            $('.stock span').text( val.goods_standard[0].stock);
            // 商品详情
            $('.detail-img-box').html(val.goods_text);
            if(val.dedicated_vehicle == null){
                $('.product-parameter').hide();
            }else{
                // 专用参数
                $('.parameter-brand').text(val.dedicated_vehicle);
                $('.parameter-series').text(val.goods_car_series.split(',').join('/ '));
                $('.parameter-year').text(val.goods_car_year.split(',').join('/ '));
                $('.parameter-displacement').text(val.goods_car_displacement.split(',').join('/ '));
            }
            // 规格值
            var specStr = '';
            $.each(val.goods_standard, function(idx, val){
                if(idx === 0){
                    // 选择服务弹窗
                    $('.select-goods-img img')[0].src = 'uploads/' + val.images;
                    $('.select-goods-price span').text(val.goods_adjusted_price);
                    $('.select-goods-stock span').text(val.stock);
                    specId = val.id;
                    specStr += `<span class="select-item select-on" id="`+val.id+`">`+val.name.split(',').join('')+`</span>`;
                }else{
                    specStr += `<span class="select-item" id="`+val.id+`">`+val.name.split(',').join('')+`</span>`;
                }
            })
            $('.select-container').append(specStr);
            $('.select-container span').click(function(){
                // 用户选择规格的index 改变图片 库存 单价
                var $index = $(this).index();
                specId = $(this).attr('id');
                $.each(val.goods_standard, function(idx, val){
                    if($index === idx){
                        $('.select-goods-img img')[0].src = 'uploads/' + val.images;
                        $('.select-goods-price span').text(val.goods_adjusted_price);
                        $('.select-goods-stock span').text(val.stock);
                    }
                })
            })
            if(val.goods_delivery !== null){
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
                $('.installation').show();
                $('.way-container').append(installationStr);
            }
            // 立即购买 身上放商品id
            $('.select-buy').prop('id', val.id);
            // 选择切换class
            $('.spec-wrap').on('click', '.select-item', function(){
                $(this).addClass('select-on');
                $(this).siblings('.select-item').removeClass('select-on');
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
    $('.pop').css('right', '0');
    $('html').css('overflow', 'hidden');
}
function hidePop(){
    $('.pop').css('right', '-100%');
    $('html').css('overflow', 'auto');
}

// 所有评论切换
$('.comment_classify_box li').click(function(){
    $(this).addClass('active').siblings().removeClass('active');
    var _index = $(this).index();
    switch(_index){
        case 0:
            allEvaluateDataAjax('goods_evaluate_return');
            break;
        case 1:
            allEvaluateDataAjax('goods_evaluate_good');
            break;
        case 2:
            allEvaluateDataAjax('goods_evaluate_secondary');
            break;
        case 3:
            allEvaluateDataAjax('goods_evaluate_bad');
            break;
        case 4:
            allEvaluateDataAjax('goods_evaluate_has_img');
            break;
    }
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
function timetrans(date){
    var date = new Date(date*1000);//如果date为13位不需要乘1000
    var Y = date.getFullYear() + '-';
    var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
    var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate()) + ' ';
    var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
    var m = (date.getMinutes() <10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
    var s = (date.getSeconds() <10 ? '0' + date.getSeconds() : date.getSeconds());
    return Y+M+D+h+m+s;
}

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
            if(res.status == 0){
                layer.open({
                    content: res.info,
                    btn: ['确定', '取消'],
                    yes: function (index) {
                        layer.close(index);
                        location.href = 'member_address_add?id='+id+'&preid='+preId+'';
                    }
                });
            }else if(res.status == 2){
                layer.open({
                    content: res.info,
                    btn: ['确定', '取消'],
                    yes: function (index) {
                        layer.close(index);
                        location.href = 'login';
                    }
                });
            }else{
                if($('.select-goods-spec').text() !== '选择规格'){
                    var goods_id = $($this)[0].id;
                    var goods_number = $('.select-calculator_val').val();
                    var goods_standard = $('.select-goods-spec').text();
                    console.log(specId)
                    $.ajax({
                        url: 'get_goods_id_save',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            'goods_id': goods_id,
                            'goods_number': goods_number,
                            'goods_standard': goods_standard,
                            'goods_standard_id': specId
                        },
                        success: function(res){
                            console.log(res);
                            location.href = 'ios_api_order_parts_firm_order?id=' + id + '&preid=' + preId;
                        },
                        error: function(){
                            console.log('error')
                        }
                    })
                }else{
                    layer.open({
                        skin: 'msg',
                        content: '请选择规格',
                        time: 1
                    })
                }
            }
        },
        error: function(){
            console.log('error');
        }
    })
})


// 关闭选择服务 弹窗
$('.close-select-pop').click(function(){
    $('html').css('overflow','auto');
    $('.mask').hide();
    $('.select-ser-pop').animate({'bottom': '-100%'});
})

// 购买数量
$(function(){
    myCalculator('increase', 'minus', 'calculator_val' );
})
// 弹窗 购买数量
$(function(){
    myCalculator('select-increase', 'select-minus', 'select-calculator_val' );
})
// 计算器
function myCalculator(addBtn, minusBtn, val){
    var value = document.getElementsByClassName(val)[0];
    var aBtn = document.getElementsByClassName(addBtn)[0];
    var mBtn = document.getElementsByClassName(minusBtn)[0];
    $(mBtn).click(function(){
        if(value.value > 1){
            value.value -= 1;
        }else{
            value.value = 1;
        }
    })
    // 加
    $(aBtn).click(function(){
        var stock = parseInt($('.select-goods-stock span').text());
        if(stock > value.value){
            var add =  value.value - 0;
            add += 1;
            value.value = add;
        }
    })
}

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

// 判断有没有登录
$(function(){
    $.ajax({
        url: 'isLogin',
        type: 'POST',
        dataType: 'JSON',
        success: function(res){
            console.log(res);
            var loginStatus = parseInt(res.status);
            // 加入购物 车选择服务 立即购买显示规格弹窗
            $('.ser-type').add('#buy').add('.add-cart').click(function(){
                if(loginStatus === 0){
                    location.href = 'login';
                }else{
                    showSpecPop();
                }
            })
            // 购买弹窗  加入购物车
            $('.select-add-cart').click(function(){
                $('.select-ser-pop').animate({'bottom': '-100%'});
                $('.mask').hide();
                var goods_id = $('.select-buy').attr('id');
                var goods_unit = $('.select-calculator_val').val();
                var goods_standard_id = $('.select-container').find('.select-on').attr('id');
                var goods_delivery = $('.way-container').find('.select-on').text();
                $.ajax({
                    url: 'get_goods_id_to_cart',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        'goods_id': goods_id,
                        'store_id': store_id,
                        'goods_unit': goods_unit,
                        'goods_standard_id': goods_standard_id,
                        'goods_delivery': goods_delivery
                    },
                    success: function(res){
                        console.log(res);
                        myLayer(res.info);
                    },
                    erro: function(){
                        console.log('error');
                    }
                })
            })
            // 加入购物车图标
            $('.cart').click(function(e){
                e.preventDefault();
                if(loginStatus === 0){
                    location.href = 'login';
                }else{
                    location.href = 'cart_index';
                }
            })
            // 收藏商品图标
            $('.collect').click(function(){
                if(loginStatus === 0){
                    location.href = 'login';
                }else{
                    var goodsId = {id: id}
                    myAjax('collection_add', goodsId);
                }
            })
        },
        error: function(){
            console.log('error');
        }
    })
})
// 选择服务 立即购买显示规格弹窗
function showSpecPop(){
    $('html').css('overflow','hidden');
    $('.mask').show();
    $('.select-ser-pop').animate({'bottom': '0'});
    $('.select-calculator_val').val($('.calculator_val').val());
    
    // 点开弹窗 规格显示 默认选中的几个
    var selectSpec = '';
    $.each($('.select-on'), function(idx, val){
        selectSpec += $(val).text() + ' ';
    })
    $('.select-goods-spec').text('规格：' + selectSpec);
}
function myLayer(info){
    layer.open({
        skin: 'msg',
        content: info,
        time: 1
    })
}
function myAjax(url, data){
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        data: data,
        success: function(res){
            console.log(res);
            if(res.status === 3){
                myLayer(res.info);
            }else{
                myLayer(res.info);
                $('.collect').toggleClass('collect-on');
            }
        },
        error: function(){
            console.log('error')
        }
    })
}

// 进入店铺
$(function(){
    $.ajax({
        url: 'go_to_store',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'goods_id': id
        },
        success: function(res){
            console.log('进入店铺',res);
            if(res.status == 1){
                var storeData = res.data.store_data;
                $('.card-title')
                    .html(`<img src="uploads/`+storeData.store_logo_images+`">
                            <span class="txt-hid-one">`+storeData.store_name+`</span>
                            <a href="store_index?storeId=`+res.data.store_id+`">进店</a>`)
                var str = '';
                res.data.goods_info.forEach(function(val, idx){
                    str += `<div>
                                <img src="uploads/`+val.goods_show_images+`">
                                <p>￥`+val.special_info[0].goods_adjusted_price+`</p>
                            </div>`
                })
                $('.card-show').html(str).click(function(){
                    location.href = `store_index?storeId=`+res.data.store_id;
                });

            }
        },
        error: function(){
            console.log('error');
        }
    })
})
// 评价
$.ajax({
    url: 'goods_evaluate_return',
    type: 'POST',
    dataType: 'JSON',
    data: {
        'goods_id': id
    },
    success: function(res){
        console.log('评价',res);
        if(res.status == 1){
            var str = '';
            $('.comment_title span').text(res.data.length);
            var data = res.data;
            var length = data.length > 3 ? 3: data.length;
            for(var i = 0; i < length; i++){
                str += `<li class="comment_li" data-evalid="`+data[i].id+`">
                            <div class="content-container">
                                <div class="comment_user_info">
                                    <div class="comment_user_headimg">
                                        <img src="userimg/`+data[i].user_info.user_img+`">
                                    </div>
                                    <div class="comment_phone_time">
                                        <div class="phone_time clearfix">
                                            <span class="phone_box">`+data[i].user_info.phone_num+`</span>
                                            <span class="time_box">`+timetrans(data[i].create_time)+`</span>
                                        </div>
                                        <span class="spr star `+(data[i].evaluate_stars === 5?'':
                                                                (data[i].evaluate_stars === 4?'four-star':
                                                                (data[i].evaluate_stars === 3?'three-star':
                                                                (data[i].evaluate_stars === 2?'two-star':'one-star'))))+`"></span>
                                    </div>
                                </div>
                                <div class="comment_text_box">
                                    <a href="javascript:;" class="comment_text_a">
                                        <p class="comment_text_p">`+data[i].evaluate_content+`</p>
                                    </a>`
                // 有评论图片
                if(data[i].images.length !== 0){
                    str += `<ul class="comment_img_ul">`;
                    data[i].images.forEach(function(ele, idx){
                        str += `<li class="comment_img_li">
                                    <img src="uploads/`+ele.images+`">
                                </li>`
                    })
                    str += '</ul>';
                }
                // 有商家回复
                if(data[i].business_repay !== null){
                    str += `<div class="reply_box">
                                <i class="triangle"></i>
                                <p class="reply_text">`+data[i].business_repay+`</p>
                            </div>`
                }
                str += `</div>
                    </div>
                    <div class="bottom_time_box">
                    <p class="buy_time">购买时间: <span>`+timetrans(data[i].order_create_time)+`</span></p>
                    <div>
                        <a href="javascript:;" class="like"><i class="spr icon_like"></i><span class="like_num">11</span></a>
                    </div>
                </div>
            </li>`
            }
            $('.more_comment_box').before(str);
            // 查看评价详情
            $('.content-container').click(function(){
                $('html').css('overflow', 'hidden');
                $('.comment-detail-pop').show();
                var id = $(this).parents('.comment_li').attr('data-evalid');
                evaluateDetailAjax(id);
            })
            $('.detail-back').click(function(){
                var ifshowPop = +$('.pop').css('right').split('px')[0];
                $('.comment-detail-pop').hide();
                if(ifshowPop == 0){
                    return;
                }
                $('html').css('overflow', 'auto');
            })
        }else{
            $('.comment_title span').text('0');
        }
    },
    error: function(){
        console.log('error');
    }
})
$('.more_comment_box').on('click','a', function(){
    showPop();
    allEvaluateDataAjax('goods_evaluate_return');
})
// 查看全部评论ajax
function allEvaluateDataAjax(url){
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        data: {
            'goods_id': id
        },
        success: function(res){
            console.log('评价',res);
            if(res.status == 1){
                var str = '';
                res.data.forEach(function(ele, idx){
                    str += `<li class="comment_li" data-evalid="`+ele.id+`">
                                <div class="all-content-container">
                                    <div class="comment_user_info">
                                        <div class="comment_user_headimg">
                                            <img src="`+(ele.user_info.user_img===null?`static/index/image/avatar.png`:`userimg/`+ele.user_info.user_img)+`">
                                        </div>
                                        <div class="comment_phone_time">
                                            <div class="phone_time clearfix">
                                                <span class="phone_box">`+ele.user_info.phone_num+`</span>
                                                <span class="time_box">`+timetrans(ele.create_time)+`</span>
                                            </div>
                                            <span class="spr star `+(ele.evaluate_stars === 5?'':
                                                                    (ele.evaluate_stars === 4?'four-star':
                                                                    (ele.evaluate_stars === 3?'three-star':
                                                                    (ele.evaluate_stars === 2?'two-star': 'one-star'))))+`"></span>
                                        </div>
                                    </div>
                                    <div class="comment_text_box">
                                        <a href="javascript:;" class="comment_text_a">
                                            <p class="comment_text_p">`+ele.evaluate_content+`</p>
                                        </a>`
                    // 有评论图片
                    if(ele.images.length !== 0){
                        str += `<ul class="comment_img_ul">`;
                        ele.images.forEach(function(val, idx){
                            str += `<li class="comment_img_li">
                                        <img src="uploads/`+val.images+`">
                                    </li>`
                        })
                        str += '</ul>';
                    }
                    // 有商家回复
                    if(ele.business_repay !== null){
                        str += `<div class="reply_box">
                                    <i class="triangle"></i>
                                    <p class="reply_text">`+ele.business_repay+`</p>
                                </div>`
                    }
                    str += `</div>
                        </div>
                        <div class="bottom_time_box">
                            <p class="buy_time">购买时间: <span>2018-11-05</span></p>
                            <div>
                                <a href="javascript:;" class="like"><i class="spr icon_like"></i><span class="like_num">11</span></a>
                            </div>
                        </div>
                    </li>`
                })
                $('.pop .comment_ul').html('').html(str);
                // 查看评价详情
                $('.all-content-container').click(function(){
                    $('html').css('overflow', 'hidden');
                    $('.comment-detail-pop').show();
                    var id = $(this).parents('.comment_li').attr('data-evalid');
                    evaluateDetailAjax(id);
                })
            }else{
                $('.pop .comment_ul').html('');
            }
        },
        error: function(){
            console.log('error');
        }
    })
}
// 查看评论详情ajax
function evaluateDetailAjax(id){
    $.ajax({
        url: 'goods_evaluate_detail',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': id
        },
        success: function(res){
            console.log(res);
            if(res.status == 1){
                var data = res.data;
                $('.detail_user_info').html(`<div class="detail_user_headimg">
                                            <img src="userimg/`+data.user_info.user_img+`">
                                        </div>
                                        <div class="detail_phone_time">
                                            <div class="phone_time clearfix">
                                                <span class="phone_box">`+data.user_info.phone_num+`</span>
                                                <span class="time_box">`+timetrans(data.evaluate_info.create_time)+`</span>
                                            </div>
                                            <span class="spr star `+(data.evaluate_info.evaluate_stars === 5?'':
                                                                    (data.evaluate_info.evaluate_stars === 4?'four-star':
                                                                    (data.evaluate_info.evaluate_stars === 3?'three-star':
                                                                    (data.evaluate_info.evaluate_stars === 2?'two-star': 'one-star'))))+`"></span>
                                        </div>`)
                $('.detail_text_p').text(data.evaluate_info.evaluate_content);
                if(data.images.length !== 0){
                    var str = '';
                    data.images.forEach(function(ele, idx){
                        str += `<img src="uploads/`+ele.images+`">`
                    })
                    $('.detail-com-img').html(str);
                }
                // 商家回复
                if(data.evaluate_info.business_repay !== null){
                    $('.detail_text_box').find('.reply_box').show().find('.reply_text').text(data.evaluate_info.business_repay);
                }
            }
            
        },
        error: function(){
            console.log('error');
        }
    })
}
// 你可能喜欢
$(function(){
    $.ajax({
        url: 'may_like_goods',
        type: 'POST',
        dataType: 'JSON',
        success: function(res){
            console.log('你可能喜欢',res);
            if(res.status == 1){
                var str = '';
                res.data.forEach(function(val, idx){
                    str += `<li class="`+(idx % 2 !== 0?'mgr0':'')+`" data-storeId="`+val.store_id+`">
                                <div class="img_div">
                                    <img src="uploads/`+val.special_info[0].images+`">
                                </div>
                                <div class="goods_name">
                                    <p class="txt-hid-two">`+val.goods_name+`</p>
                                </div>
                                <div class="goods_price">
                                    <span class="price">￥`+val.special_info[0].goods_adjusted_price+`</span>
                                    <span class="pay_num">`+val.statistical_quantity+`人购买</span>
                                </div>
                            </li>`
                })
                $('.like_cont_ul').html(str).on('click', 'li', function(){
                    location.href = 'store_index?storeId='+$(this).attr('data-storeId');
                });
            }
        },
        error: function(){
            console.log('error');
        }
    })
})

