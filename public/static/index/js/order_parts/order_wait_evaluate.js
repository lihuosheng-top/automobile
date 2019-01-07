$('.tabs button').click(function(){
    var $index = $(this).index();
    switch($index){
        case 0:
            location.href = 'order_parts_all';break;
        case 1:
            location.href = 'order_parts_wait_pay';break;
        case 2:
            location.href = 'order_wait_deliver';break;
        case 3:break;
        case 4:
            location.href = 'order_parts_return_goods';break;
    }
})
$('.icon-back').click(function(){
    location.href = 'my_index';
})

$.ajax({
    url: 'ios_api_order_wait_evaluate',
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
                            <p class="status-txt-p">待评价</p>
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
                        <p class="total-p">共计`+val.all_numbers+`件商品 合计：￥`+val.all_order_real_pay+`</p>
                        <div class="button-box">
                            <button class="del-order-btn">删除订单</button>
                            <button class="evaluation-btn">去评价</button>
                            <button class="return-goods">退货</button>
                        </div>
                    </div>
                </div>`
        })
        $('.shops-goods-wrap').append(str);

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
                    if(res.data.status === 1){
                        location.href = 'evaluate_index';
                    }
                },
                error: function(){
                    console.log('error');
                }
            })
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
        // 退款退货
        $('.return-goods').click(function(){
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