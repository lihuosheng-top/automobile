$('.tabs button').click(function(){
    var $index = $(this).index();
    switch($index){
        case 0:
            location.href = 'order_parts_all';break;
        case 1:
            location.href = 'order_parts_wait_pay';break;
        case 2:
            location.href = 'order_wait_deliver';break;
        case 3:
            location.href = 'order_wait_evaluate';break;
        case 4:break;
    }
})
$('.icon-back').click(function(){
    location.href = 'my_index';
})

$.ajax({
    url: 'ios_api_order_parts_return_goods',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        var str = '';
        var statusTxt = '';
        $.each(res.data, function(idx, val){
            if(val.status == 11){
                statusTxt = '退货';
            }else if(val.status == 13){
                statusTxt = '退货中';
            }
            str += `<div class="single-shop-box" data-id="`+val.store_id+`" data-status="`+val.status+`" name="`+val.parts_order_number+`">
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
                        <p class="total-p">共计`+val.all_numbers+`件商品 合计：￥`+val.all_order_real_pay+`</p>
                    </div>
                </div>`
        })
        $('.shops-goods-wrap').append(str);
        // 查看订单详情
        $('.all-goods-box').click(function(){
            var store_id = $(this).parents('.single-shop-box').attr('data-id');
            var parts_order_number = $(this).parents('.single-shop-box').attr('name');
            var dataStatus = $(this).parents('.single-shop-box').attr('data-status');
            $.ajax({
                url: 'order_parts_save_record',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'parts_order_number': parts_order_number,
                    'store_id': store_id,
                    'status': dataStatus
                },
                success: function(res){
                    console.log(res);
                    location.href = 'order_parts_detail?page=5'; 
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