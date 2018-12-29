$('.tabs button').click(function(){
    var $index = $(this).index();
    switch($index){
        case 0:
            location.href = 'order_service_all';break;
        case 1:
            location.href = 'order_service_wait_pay';break;
        case 2:
            location.href = 'order_service_wait_deliver';break;
        case 3:break;
        case 4:
            location.href = 'order_service_return_goods';break;
    }
})


$.ajax({
    url: 'ios_api_order_service_wait_evaluate',
    dataType: 'JSON',
    type: 'POST',
    success: function(res){
        console.log(res);
        if(res.status == 1){
            var str = '';
            $.each(res.data, function(idx, val){
                str += `<div class="reservation-tab">
                            <div class="order-num-status">
                                <div class="order-num">
                                    <i class="spr icon-order-num"></i>订单编号：`+val.service_order_number+`
                                </div>
                                <span class="status">待评价</span>
                            </div>
                            <p class="order-time">
                                <i class="spr icon-time"></i>`+val.got_to_time+`
                            </p>
                            <p class="order-shop-name">
                                <i class="spr icon-shop"></i>`+val.store_name+`
                            </p>
                            <div class="order-item">
                                <span></span>`+val.service_goods_name+`
                            </div>
                            <div class="button-box">
                                <button class="del-order-btn">删除订单</button>
                                <button class="evaluation-btn">去评价</button>
                            </div>
                        </div>`
            })
            $('.reservation-tab-container').append(str);
        }
    },
    error: function(){
        console.log();
    }
})

