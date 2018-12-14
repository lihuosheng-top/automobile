
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
var id;
if(url.indexOf('?') != -1){
    id = url.substr(1).split('=')[1];
}
// $.ajax({
//     url: 'reservation_detail',
//     type: 'POST',
//     dataType: 'JSON',
//     data: {
//         'id': id
//     },
//     success: function(data){
//         console.log(data);
//         var str = '';
//         var str2 = '';
//         $.each(data.data, function(idx, val){
//             str += '<p class="shop_name">'+val.store.store_name+'</p>\
//                     <i class="spr star"></i>\
//                     <p class="nature">'+val.service_setting_name+'</p>';
//             str2 += '<p class="addr_p"><i class="spr icon_addr"></i>'+val.store.store_detailed_address+'</p>\
//                     <p class="phone_p"><i class="spr icon_phone"></i>'+val.store.store_owner_seat_num+'</p>'
//         })
//         $('.shop_info_box').append(str);
//         $('.addr_phone_box').append(str2);
//     },
//     error: function(){
//         console.log('error');
//     }
// })

// 切换服务项目 本店商品
$('.service-tab-title').on('click', 'li', function(){
    $(this).siblings().removeClass('service-this');
    $(this).addClass('service-this');
})
$('.service-colla-title').click(function(e){
    e.preventDefault();
    $(this).find('.icon-uncheck').toggleClass('icon-check');
    
})

// 筛选评论
$('.filter-ul').on('click', 'li', function(){
    $(this).siblings().removeClass('filter-this');
    $(this).addClass('filter-this');
})
