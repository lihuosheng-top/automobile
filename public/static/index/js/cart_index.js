
var totalPrice = 0;
// 店铺选中样式
function myCircleClass(){
    $('.shop-circle').click(function(){
        $(this).toggleClass('circle-on');
        if($(this).hasClass('circle-on')){
            $(this).parents('.goods_wrap').find('.goods-circle').addClass('circle-on');
            
        }else{
            $(this).parents('.goods_wrap').find('.goods-circle').removeClass('circle-on');
        }
        calPrice();
        $('.totalprice span').text(totalPrice);
    })
    // 单选商品
    $('.goods-circle').click(function(){
        $(this).toggleClass('circle-on');
        // 遍历所有被选中商品的 单价*数量
        calPrice();
        $('.totalprice span').text(totalPrice);
    })
    // 全选
    $('.all-select').click(function(){
        $(this).toggleClass('circle-on');
        if($(this).hasClass('circle-on')){
            $('.shop-circle').add('.goods-circle').addClass('circle-on');
        }else{
            $('.shop-circle').add('.goods-circle').removeClass('circle-on');
        }
        calPrice();
        $('.totalprice span').text(totalPrice);
    })
}

function calPrice(){
    var totalPriceArr = [];
    $.each($('.goods-circle.circle-on'), function(idx, val){
        var price = parseFloat($(val).siblings().find('.cart_price span').text());
        var num = parseInt($(val).siblings().find('.calculator_val').val());
        // 存放所有计算后的价钱
        totalPriceArr.push(parseFloat(toFixed(price*num, 2)));
    })
    console.log(totalPriceArr)
    if(totalPriceArr.length !== 0){
        // 存放总价钱  利用数组的方法reduce计算总价钱，toFixed解决计算精度问题
        totalPrice = toFixed(totalPriceArr.reduce( (pre, curr) => {
            return pre + curr;
        }), 2)
        console.log(totalPrice);
    }else{
        totalPrice = 0;
    }
}
// 解决计算精度问题
function toFixed(num, s) {
    var times = Math.pow(10, s)
    var des = num * times + 0.5
    des = parseInt(des, 10) / times
    return des + ''
}

//  加减商品数量
function calculator(){
    $('.minus').click(function(){
        var val = $(this).next().val(); 
        if(val > 1){
            val --;
            $(this).siblings('input').val(val);
            var shopping_id = $(this).parents('.goods_info_wrap').find('.circle').attr('id');
            $.ajax({
                url: 'cart_information_del',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'shopping_id': shopping_id,
                    'goods_unit': 1
                },
                success: function(res){
                    console.log(res);
                },
                error: function(){
                    console.log('error');
                }
            }) 
        }
    })  
    $('.increase').click(function(){
        var val = $(this).prev().val(); 
        val ++;
        $(this).prev().val(val);
        var shopping_id = $(this).parents('.goods_info_wrap').find('.circle').attr('id');
        $.ajax({
            url: 'cart_information_add',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'shopping_id': shopping_id,
                'goods_unit': 1
            },
            success: function(res){
                console.log(res);
            },
            error: function(){
                console.log('error');
            }
        }) 
    })
}

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
    var id = [];
    $.each($('.goods-circle'), function(idx, val){
        if($(val).hasClass('circle-on')){
            id.push($(val).attr('id'));
        }
    })
    $.ajax({
        url: 'carts_del',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': id
        },
        success: function(res){
            console.log(res);
            if(res.status === 1){
                location.reload();
            }
        },
        error: function(){
            console.log('error');
        }
    }) 
})


// 获取商家的信息，如果存在则是商家角色，不存在则为车主
$.ajax({
    url: 'select_role_get',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log('获取商家的信息，如果存在则是商家角色，不存在则为车主',res);
        $('.my').click(function(){
            if(res.status == 1){
                location.href = 'sell_my_index';
            }else{
                location.href = 'my_index';
            }
        })
    },
    error: function(){
        console.log('error');
    }
})

// 购物车页面信息
$.ajax({
    url: 'cart_index',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        var str = '';
        if(res.status === 1){
            $.each(res.data, function(idx, val){
                str += `<div class="goods_wrap">
                            <div class="shop_name_wrap">
                                <i class="spr circle shop-circle"></i>
                                <a href="javascript:;">
                                    <i class="spr icon_shop"></i>
                                    <span class="shop_name">`+val.store_name+`</span>
                                    <i class="spr icon_more"></i>
                                </a>
                            </div>`
                $.each(val.info, function(idx, val){
                    str += `<div class="goods_info_wrap">
                                <i class="spr circle goods-circle" id="`+val.id+`"></i>
                                <div class="goods_detail_info">
                                    <div class="goods_img_wrap">
                                        <a href="javascript:;">
                                            <img src="uploads/`+val.goods_images+`">
                                        </a>
                                    </div>
                                    <div class="goods_desc_right">
                                        <p class="cart_goods_name">`+val.goods_name+`</p>
                                        <p class="cart_sub1">`+val.special_name.split(',').join('')+`</p>
                                        <div class="price_num_wrap">
                                            <span class="cart_price">￥<span>`+val.goods_prices+`</span></span>
                                            <div class="calculator_num">
                                                <a href="javascript:;" class="minus">-</a>
                                                <input type="text" value="`+val.goods_unit+`" class="calculator_val" readonly>
                                                <a href="javascript:;" class="increase">+</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`
                })
                str += `</div>`;
            })
            $('.exist').prepend(str);
            calculator();
            myCircleClass();
        }
    },
    error: function(){
        console.log('error');
    }
})













