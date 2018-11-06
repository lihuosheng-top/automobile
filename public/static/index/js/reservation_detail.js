
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
$('html').bind('touchmove', function(e){
    if($(this).scrollTop() > 0){
        $('.wrapper .head').css('background', 'rgba(255, 255, 255, .5)');
    }else{
        $('.wrapper .head').css('background', 'transparent');
    }
})

// 所有评论切换
$('.comment_classify_box').on('tap', 'li', function(){
    $(this).addClass('active').siblings().removeClass('active');
})

// 图片展示 start
var index, curr_container, curr_img_len;
$('.wrapper .comment_img_ul').on('tap', '.comment_img_li', function(e){
    index = $(this).index();// 当前被点击的索引值
    curr_container = e.liveFired;//被点击li的父级
    curr_img_len = $(curr_container).children().length;//被点击容器中li的length
    $('.show_img .img_num').html(index+1+'/'+curr_img_len);//显示图片数量
    loadImage(e.target);//执行 传参
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
$('.img_box').on('tap', function(e){
    if(!$(e.targeet).hasClass('.img_box')){
        $('.show_img').hide();
    }
    $('html').css('overflow', 'auto');
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

