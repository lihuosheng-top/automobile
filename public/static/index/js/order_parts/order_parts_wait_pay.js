$('.tabs button').click(function(){
    var $index = $(this).index();
    switch($index){
        case 0:
            location.href = 'order_parts_all';break;
        case 1:break;
        case 2:
            location.href = 'order_wait_deliver';break;
        case 3:
            location.href = 'order_wait_evaluate';break;
        case 4:
            location.href = 'order_parts_return_goods';break;
    }
})
$('.icon-back').click(function(){
    location.href = 'my_index';
})

$.ajax({
    url: 'ios_api_order_parts_wait_pay',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        var str = '';
        $.each(res.data, function(idx, val){
            str += `<div class="single-shop-box" data-id="`+val.store_id+`" name="`+val.parts_order_number+`">
                        <div class="shop-name-box">
                            <div class="name-txt-div">
                                <i class="spr icon-shop"></i>
                                <span class="name-txt-span">`+val.store_name+`</span>
                            </div>
                            <p class="status-txt-p">待付款</p>
                        </div>`
            $.each(val.info, function(idx, val){
                str += `<div class="all-goods-box">
                            <div class="single-goods-box single-goods-box-border">
                                <div class="shop-img-box">
                                    <img src="uploads/`+val.goods_image+`">
                                </div>
                                <div class="order-info-box">
                                    <p class="goods-name-p txt-hid-two">`+val.parts_goods_name+`</p>
                                    <p class="goods-selling-point txt-hid-two">`+val.parts_goods_name+`</p>
                                    <div class="unit-price-quantity">
                                        <p class="unit-price-p">￥`+val.goods_money+`</p>
                                        <p class="quantity-p">×`+val.order_quantity+`</p>
                                    </div>
                                </div>
                            </div>
                        </div>`
            })
            str += `<div class="total-button-box">
                        <p class="total-p">共计`+val.all_numbers+`件商品 合计：￥<span class="total-money-span">`+val.all_order_real_pay+`<span></p>
                        <div class="button-box">
                            <button class="cancel-order-btn">取消订单</button>
                            <button class="to-payment-btn">去付款</button>
                        </div>
                    </div>
                </div>`
        })
        $('.shops-goods-wrap').append(str);
        // 取消订单
        $('.cancel-order-btn').click(function(){
            var store_id = $(this).parents('.single-shop-box').attr('data-id');
            var parts_order_number = $(this).parents('.single-shop-box').attr('name');
            $('.cancel-order-pop').animate({'bottom': '0'});
            $('.mask').show();
            $('.select-reason-btn').click(function(){
                var cancel_order_description = $('.reason-selected')[0].innerText;
                $.ajax({
                    url: 'ios_api_order_parts_no_pay_cancel',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        'parts_order_number': parts_order_number,
                        'store_id': store_id,
                        'cancel_order_description': cancel_order_description
                    },
                    success: function(res){
                        console.log(res);
                        location.reload();
                    },
                    error: function(){
                        console.log('error');
                    }
                })
            })
        })
        // 选择取消订单原因 
        $('.reason-li').click(function(){
            $(this).addClass('reason-selected').siblings().removeClass('reason-selected');
        })
        $('.close-cancel-order').click(function(){
            $('.cancel-order-pop').animate({'bottom': '-100%'});
            $('.mask').hide();
        })
        // 去付款 √
        $('.to-payment-btn').click(function(){
            $('.mask').show();
            $('.alipay-pop').animate({ 'bottom': '0' });
            $('html').css('overflow', 'hidden');
            // 付款金额
            var totalAmount = $(this).parents('.total-button-box').find('.total-money-span').text();
            var outTradeNo = $(this).parents('.single-shop-box').attr('name');
            var subjuect = $(this).parents('.single-shop-box').find('.goods-name-p').text();
            var body = $(this).parents('.single-shop-box').find('.goods-selling-point').text();
            $('#WIDtotal_amount').val(totalAmount);
            $('#WIDout_trade_no').val(outTradeNo);
            $('#WIDsubject').val(subjuect);
            $('#WIDbody').val(body);

            $('.close-alipay').click(function () {
                $('.mask').hide();
                $('.alipay-pop').animate({ 'bottom': '-100%' });
                $('html').css('overflow', 'auto');
            })
        })
        // 查看订单详情
        $('.all-goods-box').click(function(){
            var store_id = $(this).parents('.single-shop-box').attr('data-id');
            var parts_order_number = $(this).parents('.single-shop-box').attr('name');
            $.ajax({
                url: 'order_parts_save_record',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'parts_order_number': parts_order_number,
                    'store_id': store_id
                },
                success: function(res){
                    console.log(res);
                    location.href = 'order_parts_detail'; 
                },
                error: function(){
                    console.log('error');
                }
            })
        })
    },
    error: function(){
        console.log('error');
    }
})