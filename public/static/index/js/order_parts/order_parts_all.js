
$.ajax({
    url: 'ios_api_order_parts_all',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        var str = '';
        var statusTxt = '';
        $.each(res.data, function(idx, val){
            if(val.status === 1){
                statusTxt = `待付款`;
            }else if(val.status === 2 || val.status === 3 || val.status === 4 || val.status === 5){
                statusTxt = `待收货`;
            }else if(val.status === 6 || val.status === 7){
                statusTxt = `待评价`;
            }else if(val.status === 8){
                statusTxt = `已完成`;
            }else if(val.status === 9 || val.status === 10){
                statusTxt = `已取消`;
            }else if(val.status === 11){
                statusTxt = `退货`;
            }
            str += `<div class="single-shop-box" data-id="`+val.store_id+`" name="`+val.parts_order_number+`">
                        <div class="shop-name-box">
                            <div class="name-txt-div">
                                <i class="spr icon-shop"></i>
                                <span class="name-txt-span">`+val.store_name+`</span>
                            </div>
                            <p class="status-txt-p">`+statusTxt+`</p>
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
                        <p class="total-p">共计<span>`+val.all_numbers+`</span>件商品 合计：￥<span class="total-money-span">`+val.all_order_real_pay+`</span></p>`

            if(statusTxt == '待付款'){
                str +=`<div class="button-box">
                            <button class="cancel-order-btn">取消订单</button>
                            <button class="to-payment-btn">去付款</button>
                        </div>
                    </div>
                </div>`
            }else if(statusTxt == '待收货'){
                str +=`<div class="button-box">
                            <button class="check-logistics-btn">查看物流</button>
                            <button class="conf-receipt-btn">确认收货</button>
                        </div>
                    </div>
                </div>`
            }else if(statusTxt == '待评价'){
                str +=`<div class="button-box">
                            <button class="del-order-btn">删除订单</button>
                            <button class="evaluation-btn">去评价</button>
                        </div>
                    </div>
                </div>`
            }else if(statusTxt == '已完成'){
                str +=`<div class="button-box">
                            <button class="del-order-btn">删除订单</button>
                        </div>
                    </div>
                </div>`
            }else if(statusTxt == '已取消'){
                str +=`<div class="button-box">
                            <button class="del-order-btn">删除订单</button>
                        </div>
                    </div>
                </div>`
            }else if(statusTxt == '退货'){
                str +=`</div>
                    </div>`
            }
        })
        $('.shops-goods-wrap').append(str);

        // 取消订单 √
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
        // 删除订单 √
        $('.del-order-btn').click(function(){
            var store_id = $(this).parents('.single-shop-box').attr('data-id');
            var parts_order_number = $(this).parents('.single-shop-box').attr('name');
            layer.open({
                content: '您确定删除订单？',
                btn: ['确定', '取消'],
                yes: function (index) {
                    layer.close(index);
                    $.ajax({
                        url: 'ios_api_order_parts_del',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            'parts_order_number': parts_order_number,
                            'store_id': store_id
                        },
                        success: function(res){
                            console.log(res);
                            location.reload();
                        },
                        error: function(){
                            console.log('error');
                        }
                    })
                }
            });
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
        // 确认收货 √
        $('.conf-receipt-btn').click(function(){
            var store_id = $(this).parents('.single-shop-box').attr('data-id');
            var parts_order_number = $(this).parents('.single-shop-box').attr('name');
            layer.open({
                content: '您确认收货？',
                btn: ['确认', '取消'],
                yes: function (index) {
                    layer.close(index);
                    $.ajax({
                        url: 'ios_api_order_parts_collect_goods',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            'parts_order_number': parts_order_number,
                            'store_id': store_id
                        },
                        success: function(res){
                            console.log(res);
                            location.reload();
                        },
                        error: function(){
                            console.log('error');
                        }
                    })
                }
            });
        })
        // 查看订单详情 √
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
        // 去评价
        $('.evaluation-btn').click(function(){
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
                    location.href = 'evaluate_index';
                },
                error: function(){
                    console.log('error');
                }
            })
        })
        // 查看物流
        $('.check-logistics-btn').click(function(){
            var store_id = $(this).parents('.single-shop-box').attr('data-id');
            var parts_order_number = $(this).parents('.single-shop-box').attr('name');
            $.ajax({
                url: 'ios_api_order_parts_no_pay_cancel',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'parts_order_number': parts_order_number,
                    'store_id': store_id
                },
                success: function(res){
                    console.log(res);
                    location.href = 'logistics_index';
                },
                error: function(){
                    console.log('error');
                }
            })
        })
        
    },
    error: function(){
        console.log('err');
    }
})

$('.tabs button').click(function(){
    var $index = $(this).index();
    switch($index){
        case 0:break;
        case 1:
            location.href = 'order_parts_wait_pay';break;
        case 2:
            location.href = 'order_wait_deliver';break;
        case 3:
            location.href = 'order_wait_evaluate';break;
        case 4:
            location.href = 'order_parts_return_goods';break;
    }
})


