
// 全部弹窗
function showPop(){
    $('.pop').css('transform', 'translateX(0)');
    $('.wrapper').hide();
    var displayFlag = $('.comment_title').css('display');
    if(displayFlag == 'none'){
        var settingId = $('.filter-ul li:eq(0)').attr('data-serverid');
        myEvaluate(settingId,storeId, '.pop .comment_ul', true, 'reservation_evaluate_return');
    }else{
        var settingId = $('.service-colla-item').attr('data-goodsid');
        myEvaluate(settingId,storeId, '.pop .comment_ul', true, 'reservation_evaluate_return');
    }
}
function hidePop(){
    $('.pop').css('transform', 'translateX(100%)');
    $('.wrapper').show();
}
$('.filter-service-ul').on('click', 'li', function(){
    $(this).siblings().removeClass('filter-service-li');
    $(this).toggleClass('filter-service-li');
    var serveSettingId = $(this).attr('data-serverid');
    // 评论
    myEvaluate(serveSettingId,storeId, '.pop .comment_ul', true, 'reservation_evaluate_return');
    // 评论数量
    evaluateNum(serveSettingId, storeId);
})
// 全部 好评 中评 差评
$('.filter-com-ul').on('click', 'li', function(){
    $(this).siblings().removeClass('filter-li-this');
    $(this).toggleClass('filter-li-this');
    var $index = $(this).index();
    var serveSettingId = $('.filter-service-li').attr('data-serverid');
    switch($index){
        case 0:
            myEvaluate(serveSettingId,storeId, '.pop .comment_ul', true, 'reservation_evaluate_return');
            break;
        case 1:
            myEvaluate(serveSettingId,storeId, '.pop .comment_ul', true, 'reservation_evaluate_good');
            break;
        case 2:
            myEvaluate(serveSettingId,storeId, '.pop .comment_ul', true, 'reservation_evaluate_secondary');
            break;
        case 3:
            myEvaluate(serveSettingId,storeId, '.pop .comment_ul', true, 'reservation_evaluate_bad');
            break;
        case 4:
            myEvaluate(serveSettingId,storeId, '.pop .comment_ul', true, 'reservation_evaluate_has_img');
            break;
    }
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

// 获取url地址id
var url = location.search;
var storeId, serviceSettingId, avtivity,settingIdName;
var urlLen = url.substr(1).split('&').length;

if(url.indexOf('?') != -1){
    storeId = url.substr(1).split('&')[0].split('=')[1];
}
if(urlLen > 1){
    if(url.substr(1).split('&')[1].split('=')[0] == 'service_setting_id'){
        settingIdName = url.substr(1).split('&')[1].split('=')[0];
        // 选择服务类型进来
        serviceSettingId = url.substr(1).split('&')[1].split('=')[1];
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
                    var str = myGoods(data);
                    $('.goods-content').prepend(str);
                    $('.goods-colla-item').click(function(){
                        var id = $(this).attr('data-id');
                        var standid = $(this).attr('data-standid');
                        var storeid = $(this).attr('data-storeid');
                        location.href = `goods_detail?id=`+id+`&preid=`+standid+`&storeid=`+storeid+`&hot=1`+`&service_setting_id=`+serviceSettingId;
                    })
                    // 服务项目
                    var str2 = myService(data);
                    $('.service-content').append(str2);
                    $('.bespeak-money').text('￥'+$('.service-colla-content').find('.icon-check').prev().find('.sale span').text());
                    selectEvent();
    
                    $('.comment_title').show();
                    evaAjax(data);
                }
            },
            error: function(){
                console.log('error');
            }
        })
        // 从预约服务进来 返回上一页
        $('.wrapper .back').click(function(){
            location.href = 'reservation?service_setting_id='+serviceSettingId;
        })
    }else{
        // 从首页活动广告进来
        $('.activity-contact').show();
        $('.activity-section').show();
        (function(){
            return $.ajax({
                url: 'index_shop_goods',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'id': storeId
                }
            })
        })().then(function(data){
            console.log('店铺商品',data);
            if(data.status == 1 || data.status == 2){
                // 商品
                var str = myGoods(data);
                $('.goods-content').prepend(str);
                $('.goods-colla-item').click(function(){
                    var id = $(this).attr('data-id');
                    var standid = $(this).attr('data-standid');
                    var storeid = $(this).attr('data-storeid');
                    location.href = `goods_detail?id=`+id+`&preid=`+standid+`&storeid=`+storeid+`&hot=1`;
                })
                // 服务项目
                var str2 = myService(data);
                $('.service-content').append(str2);
                selectEvent();
                
                $('.comment-filter').show();
                $('.filter-service').show();
                $('.pop .comment_box .comment_ul').css('height', '69vh');
                evaAjax(data);
                // 电话 导航
                var seatNum = data.data.store[0].store_owner_seat_num;
                var storeAjax = data.data.store[0];
                $('.activity-contact .phone').attr('href', "javascript:Android.call("+seatNum+");");
                $('.activity-contact .phone-number').text(seatNum);
                $('.activity-contact .navigation').click(function(){
                    Android.openGdMap(storeAjax.latitude, storeAjax.longitude, storeAjax.store_name)
                })
                $('.activity-section .start-time span').text(timetrans(storeAjax.start_time));
                $('.activity-section .end-time span').text(timetrans(storeAjax.end_time));
                $('.rich-text').html(storeAjax.advert_text);
            }
        }, function(res){
            console.log(res.status, res.statusText);
        })

        $('.wrapper .back').click(function(){
            location.href = 'index';
        })
    }
}else{
    // 首页热门店铺进来
    $('.bespeak-btn').prop('disabled', true);
    $.ajax({
        url: 'index_shop_goods',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': storeId
        },
        success: function(data){
            console.log('获取店铺商品',data);
            if(data.status == 1 || data.status == 2){
                // 商品
                var str = myGoods(data);
                $('.goods-content').prepend(str);
                $('.goods-colla-item').click(function(){
                    var id = $(this).attr('data-id');
                    var standid = $(this).attr('data-standid');
                    var storeid = $(this).attr('data-storeid');
                    location.href = `goods_detail?id=`+id+`&preid=`+standid+`&storeid=`+storeid+`&hot=1`;
                })
                // 服务项目
                var str2 = myService(data);
                $('.service-content').append(str2);
                selectEvent();
                
                $('.comment-filter').show();
                $('.filter-service').show();
                $('..pop .comment_box .comment_ul').css('height', '69vh');
                evaAjax(data);
            }
        },
        error: function(){
            console.log('error');
        }
    })
    // 从热门店铺进来 返回上一页
    $('.wrapper .back').click(function(){
        location.href = 'index';
    })
}
// 评论铺数据
function evaAjax(data){
    var filterStr = '', allcomStr = '';
    data.data.serve_data.forEach(function(ele, idx){
        if(ele.server_name === null){
            return false;
        }
        if(idx === 0){
            filterStr += `<li class="filter-this" data-serverid="`+ele.service_setting_id+`">
                            <p class="com-type">`+(ele.serve_name.slice(2))+`</p>
                        </li>`
            allcomStr += `<li class="filter-service-li" data-serverid="`+ele.service_setting_id+`">
                                <p class="com-name">`+(ele.serve_name.slice(2))+`</p>
                            </li>`
            myEvaluate(ele.service_setting_id,storeId, '.filter-comment', false,'reservation_evaluate_return');
            evaluateNum(ele.service_setting_id, storeId);
        }else{
            filterStr += `<li data-serverid="`+ele.service_setting_id+`">
                            <p class="com-type">`+(ele.serve_name.slice(2))+`</p>
                        </li>`
            allcomStr += `<li data-serverid="`+ele.service_setting_id+`">
                            <p class="com-name">`+(ele.serve_name.slice(2))+`</p>
                        </li>`
        }
    })
    $('.filter-ul').html(filterStr);
    $('.filter-service-ul').html(allcomStr);
}
// 评论数量
function evaluateNum(settingId, storeId){
    $.ajax({
        url: 'reservation_evaluate_numbers',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'setting_id': settingId,
            'store_id': storeId
        },
        success: function(res){
            console.log('评论数量', res);
            if(res.status == 1){
                var data = res.data;
                $('.filter-com-ul').html(`<li class="filter-li-this">全部（<span>`+data[0]+`</span>）</li>
                                        <li>好评（<span>`+data[1]+`</span>）</li>
                                        <li>中评（<span>`+data[2]+`</span>）</li>
                                        <li class="negative">差评（<span>`+data[3]+`</span>）</li>
                                        <li>有图（<span>`+data[4]+`</span>）</li>`)
                if(!$('.comment_title').is(':hidden')){
                    $('.comment_title').find('span').text(data[0]);
                }
            }
        },
        error: function(){
            console.log('error');
        }
    })
}
// 评论
function myEvaluate(settingid, storeid, content, flag, url){
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        data: {
            'goods_id': settingid,
            'store_id': storeid
        },
        success: function(res){
            console.log('评论', res);
            var data = res.data;
            var str = '';
            var length = data.length > 3 ? 3 : data.length;
            for(var i = 0; i < (flag ? data.length : length); i++){
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
                        <a href="javascript:;" class="like">
                            <i class="spr icon_like `+
                            (data[i].is_praise == 1 ? 'like-on':'')
                            +`"></i><span class="like_num">`+data[i].praise+`</span>
                        </a>
                    </div>
                </div>
            </li>`
            }
            $(content).html('').html(str);
            // 评论点赞
            $('.like').click(function(){
                if($(this).find('.icon_like').hasClass('like-on')){
                    layer.open({
                        skin: 'msg',
                        content: '评论已点亮',
                        time: 1
                    })
                }else{
                    var evaluate_id = $(this).parents('.comment_li').attr('data-evalid');
                    $.ajax({
                        url: 'evaluate_service_praise',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            evaluate_id: evaluate_id
                        },
                        success: function(res){
                            console.log(res)
                            if(res.status == 2){
                                location.href = 'login';
                            }else{
                                var likeNum = $(this).find('.like_num').text();
                                $(this).find('.icon_like').addClass('like-on').siblings().text(+likeNum+1);
                            }
                        },
                        error: function(){
                            console.log('error');
                        }
                    })
                }
            })
        },
        error: function(){
            console.log('error');
        }
    })
}

// 商品
function myGoods(data){
    var str = '';
    $.each(data.data.goods, function(idx, val){
        str += `<div class="goods-colla-item" data-id="`+val.id+`" data-standid="`+val.goods_brand_id+`" data-storeid="`+val.store_id+`">
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
    return str;
}
// 服务项目 选择服务类型进来
function myService(data){
    var str2 = '';
    var swiperImgArr = [];
    $.each(data.data.store, function(idx, val){
        $('.shop_name').text(val.store_name);
        $('.addr_p span').text(val.store_detailed_address);
        $('.shop-describe').text(val.store_information);
        if(val.verifying_physical_storefront_one !== null){
            swiperImgArr.push(val.verifying_physical_storefront_one);
        }
        if(val.verifying_physical_storefront_two !== null){
            var storeInnerImg = val.verifying_physical_storefront_two.split(',').splice(0, 3);
            $.each(storeInnerImg, function(idx, val){
                swiperImgArr.push(val);
            }) 
        }
    })
    // 轮播图
    var myStr = '';
    if(swiperImgArr.length !== 0){
        $.each(swiperImgArr, function(idx, val){
            myStr += `<div class="swiper-slide">
                        <img src="uploads/`+val+`">
                    </div>`
        })
        $('.swiper-wrapper').append(myStr);
        mySwiper();
    }
    $.each(data.data.serve_data, function(idx, val){
        if(val.serve_name === null){
            return false;
        }
        str2 += `<div class="service-colla-item" data-goodsid="`+val.service_setting_id+`">
                    <div class="service-colla-title">
                        <p class="service-subtitle">`+val.serve_name+`</p>
                        <p class="service-money"></p>
                        <i class="spr icon-uncheck `+(settingIdName=='service_setting_id'?'icon-check':'')+`" id="setting-`+val.serve_goods[0].service_setting_id+`"></i>
                    </div>
                    <div class="service-colla-content" style="display:`+(settingIdName=='service_setting_id'?'block':'none')+`;">
                        <ul>`
        $.each(val.serve_goods, function(idx, val){
            if(val.service_money === null && val.ruling_money === null){
                str2 += `<li>
                            <p class="service-car-type">`+val.vehicle_model+`</p>
                            <div class="content-money-div">
                                <p class="sale"><span>面议</span></p>
                            </div>
                            <i class="spr icon-uncheck `+(settingIdName=='service_setting_id'&&idx==0?'icon-check':'')+`" id="`+val.id+`"></i>
                        </li>`
            }else if(val.service_money !== null && val.ruling_money === null){
                str2 += `<li>
                            <p class="service-car-type">`+val.vehicle_model+`</p>
                            <div class="content-money-div">
                                <p class="sale">￥<span>`+val.service_money+`</span></p>
                            </div>
                            <i class="spr icon-uncheck `+(settingIdName=='service_setting_id'&&idx==0?'icon-check':'')+`" id="`+val.id+`"></i>
                        </li>`
            }else if(val.service_money !== null && val.ruling_money !== null){
                str2 += `<li>
                            <p class="service-car-type">`+val.vehicle_model+`</p>
                            <div class="content-money-div">
                                <p class="sale">￥<span>`+val.service_money+`</span></p>
                                <p class="thro">￥<span>`+val.ruling_money+`</span></p>
                            </div>
                            <i class="spr icon-uncheck `+(settingIdName=='service_setting_id'&&idx==0?'icon-check':'')+`" id="`+val.id+`"></i>
                        </li>`
            }
        })
        str2 += `</ul>
            </div>
        </div>`
    })
    return str2;
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
            $(this).siblings().find('.icon-uncheck').removeClass('icon-check');
            $('.bespeak-btn').prop('disabled', 'disabled');
            $(this).siblings('.service-colla-content').hide();
        }
    })
    $('.service-colla-content li').click(function(){
        $(this).find('.icon-uncheck').toggleClass('icon-check');
        if($(this).find('.icon-uncheck').hasClass('icon-check')){
            $(this).siblings().find('.icon-uncheck').removeClass('icon-check');
            $('.bespeak-btn').removeAttr('disabled');
            var userSelectMoney = $(this).find('.sale').text();
            // console.log(userSelectMoney);
            $('.bespeak-money').text(userSelectMoney);
        }else{
            $('.bespeak-btn').prop('disabled', 'disabled');
            $('.bespeak-money').text('');
        }
    })
}
// swiper
function mySwiper(){
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
    var serveSettingId = $(this).attr('data-serverid');
    // 评论
    myEvaluate(serveSettingId,storeId, '.filter-comment', false, 'reservation_evaluate_return');
})

// 确定预约
$('.bespeak-btn').click(function(){
    var id = $('.service-colla-content').find('.icon-check').attr('id');
    var settingId = $('.service-colla-title').find('.icon-check').attr('id').split('-')[1];
    $.ajax({
        url: 'reservation_info',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': id
        },
        success: function(res){
            console.log(res);
            if(res.status == 1){
                if(res.data.user_car_message.length !== 0){
                    if(settingIdName=='service_setting_id'){
                        location.href = 'reservation_info?store_id='+storeId+'&serve_goods_id='+id+'&service_setting_id='+serviceSettingId;
                    }else{
                        location.href = 'reservation_info?store_id='+storeId+'&serve_goods_id='+id+'&service_setting_id='+settingId;
                    }
                }else{
                    layer.open({
                        skin: 'msg',
                        content: '未给爱车添加详细信息',
                        time: .8
                    })
                    setTimeout(function(){
                        location.href = 'love_list';
                    }, 1000)
                }
            }else if(res.status == 2){
                location.href = 'login';
            }
        },
        error: function(){
            console.log('error');
        }
    })
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