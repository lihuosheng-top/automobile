$('.tabs button').click(function(){
    var $index = $(this).index();
    switch($index){
        case 0:
            location.href = 'order_service_all';break;
        case 1:
            location.href = 'order_service_wait_pay';break;
        case 2:
            location.href = 'order_service_wait_deliver';break;
        case 3:
            location.href = 'order_service_wait_evaluate';break;
        case 4:
            location.href = 'order_service_return_goods';break;
    }
})

$('.reservation-tab').click(function(){
    location.href = 'order_service_detail';
})

$.ajax({
    url: 'ios_api_order_service_all',
    dataType: 'JSON',
    type: 'POST',
    success: function(res){
        console.log(res);
        if(res.status == 1){
            var str = '';
            var statusTxt = '';
            $.each(res.data, function(idx, val){
                if(val.status === 0 || val.status === 9 || val.status === 10){
                    statusTxt = '已关闭';
                }else if(val.status === 1){
                    statusTxt = '待付款';
                }else if(val.status === 2 || val.status === 3){
                    statusTxt = '待服务';
                }else if(val.status === 4 || val.status === 5){
                    statusTxt = '待评价';
                }else if(val.status === 6){
                    statusTxt = '已完成';
                }
                str += `<div class="reservation-tab">
                            <div class="order-num-status">
                                <div class="order-num">
                                    <i class="spr icon-order-num"></i>订单编号：`+val.service_order_number+`
                                </div>
                                <span class="status">`+statusTxt+`</span>
                            </div>
                            <p class="order-time">
                                <i class="spr icon-time"></i>`+val.got_to_time+`
                            </p>
                            <p class="order-shop-name">
                                <i class="spr icon-shop"></i>`+val.store_name+`
                            </p>
                            <div class="order-item">
                                <span></span>`+val.service_goods_name+`
                            </div>`
                if(statusTxt == '已关闭'){
                    str += `</div>`
                }else if(statusTxt == '待付款'){
                    str += `<div class="button-box">
                                <button class="cancel-order-btn">取消订单</button>
                                <button class="to-payment-btn">去付款</button>
                            </div>
                        </div>`
                }else if(statusTxt == '待服务'){
                    str += `<div class="button-box">
                                <button class="conf-receipt-btn" style="display:none;">已服务</button>
                            </div>
                        </div>`
                }else if(statusTxt == '待评价'){
                    str += `<div class="button-box">
                                <button class="del-order-btn" style="display:none;">删除订单</button>
                                <button class="evaluation-btn" style="display:none;">去评价</button>
                            </div>
                        </div>`
                }else if(statusTxt == '已完成'){
                    str += `</div>`
                }
            })
            $('.reservation-tab-container').append(str);
        }
    },
    error: function(){
        console.log('error');
    }
})







