
























// 店铺选中样式
// console.log($('.shop-circle'))
$('.shop-circle').click(function(){
    $(this).toggleClass('circle-on');
    if($(this).hasClass('circle-on')){
        $(this).parents('.goods_wrap').find('.goods-circle').addClass('circle-on');
    }else{
        $(this).parents('.goods_wrap').find('.goods-circle').removeClass('circle-on');
    }
})
// 单选商品
$('.goods-circle').click(function(){
    $(this).toggleClass('circle-on');
})
// 全选
$('.all-select').click(function(){
    $(this).toggleClass('circle-on');
    if($(this).hasClass('circle-on')){
        $('.shop-circle').add('.goods-circle').addClass('circle-on');
    }else{
        $('.shop-circle').add('.goods-circle').removeClass('circle-on');
    }
})

//  加减商品数量
$('.minus').click(function(){
    var val = $(this).next().val(); 
    if(val > 1){
        val --;
        $(this).siblings('input').val(val);
    }
})  
$('.increase').click(function(){
    var val = $(this).prev().val(); 
    val ++;
    $(this).prev().val(val); 
})

// 编辑  完成切换
$('.edit').click(function(){
    $(this).hide();
    $('.done').show();
    $('.settle_button').hide();
    $('.del-btn').show();
})
$('.done').click(function(){
    $(this).hide();
    $('.edit').show();
    $('.settle_button').show();
    $('.del-btn').hide();
})

// 结算 删除
$('.settle_button').click(function(){

})
$('.del-btn').click(function(){
    
})
















