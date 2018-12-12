
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
            str += `<div class="single-shop-box">
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
                        <p class="total-p">共计`+val.order_quantity+`件商品 合计：￥`+val.order_real_pay+`</p>
                        <div class="button-box">
                            <button class="cancel-order-btn">取消订单</button>
                            <button class="to-payment-btn">去付款</button>
                            <button class="del-order-btn" style="display:none;">删除订单</button>
                            <button class="check-logistics-btn" style="display:none;">查看物流</button>
                            <button class="conf-receipt-btn" style="display:none;">确认收货</button>
                            <button class="evaluation-btn" style="display:none;">去评价</button>
                        </div>
                    </div>`
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

