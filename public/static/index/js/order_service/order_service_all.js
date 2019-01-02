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
                str += `<div class="reservation-tab" id="`+val.id+`">
                            <div class="reservation-info-container">
                                <div class="order-num-status">
                                    <div class="order-num">
                                        <i class="spr icon-order-num"></i>订单编号：<span>`+val.service_order_number+`</span>
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
                                </div>
                            </div>`
                if(statusTxt == '已关闭'){
                    str += `<div class="button-box">
                                <button class="del-order-btn">删除订单</button>
                            </div>
                        </div>`
                }else if(statusTxt == '待付款'){
                    str += `<div class="button-box">
                                <button class="cancel-order-btn">取消订单</button>
                                <button class="to-payment-btn">去付款</button>
                            </div>
                        </div>`
                }else if(statusTxt == '待服务'){
                    str += `<div class="button-box">
                                <button class="conf-receipt-btn">已服务</button>
                            </div>
                        </div>`
                }else if(statusTxt == '待评价'){
                    str += `<div class="button-box">
                                <button class="del-order-btn">删除订单</button>
                                <button class="evaluation-btn">去评价</button>
                            </div>
                        </div>`
                }else if(statusTxt == '已完成'){
                    str += `<div class="button-box">
                                <button class="del-order-btn">删除订单</button>
                            </div>
                        </div>`
                }
            })
            $('.reservation-tab-container').append(str);

            // 取消订单
            $('.cancel-order-btn').click(function(){
                var id = $(this).parents('.reservation-tab').attr('id');
                btnEvent('确认取消订单？', 'ios_api_order_service_no_pay_cancel', id);
            })
            // 已服务 
            $('.conf-receipt-btn').click(function(){
                var id = $(this).parents('.reservation-tab').attr('id')
                btnEvent('服务已完成？', 'ios_api_order_service_already_served', id);
            })
            // 删除订单
            $('.del-order-btn').click(function(){
                var id = $(this).parents('.reservation-tab').attr('id')
                btnEvent('确认删除？', 'service_del', id);
            })
            // 去评价
            $('.evaluation-btn').click(function(){
                var orderNum = $(this).parents('.reservation-tab').find('.order-num span').text();
                detailAndEva('order_service_save_record', orderNum, 'service_evaluate_index');
            })
            // 查看详情
            $('.reservation-info-container').click(function(){
                var orderNum = $(this).parents('.reservation-tab').find('.order-num span').text();
                detailAndEva('order_service_save_record', orderNum, 'order_service_detail');
            })
            // 去付款
            $('.to-payment-btn').click(function(){
                var id = $(this).parents('.reservation-tab').attr('id')
                var index = res.data.findIndex(function(item){
                    return item.id == id;
                });
                var totalAmount = res.data[index].service_real_pay;
                var outTradeNo = res.data[index].service_order_number;
                var subjuect = res.data[index].service_goods_name;
                var body = res.data[index].service_goods_name;
                $('#WIDtotal_amount').val(totalAmount);
                $('#WIDout_trade_no').val(outTradeNo);
                $('#WIDsubject').val(subjuect);
                $('#WIDbody').val(body);
                if(totalAmount !== 0){
                    $('.mask').show();
                    $('.alipay-pop').animate({'bottom': '0'});
                    $('html').css('overflow', 'hidden');
                }else{
                    layer.open({
                        skin: 'msg',
                        content: '等待商家确认，确认之后方可付款！',
                        time: 1.2
                    })
                }

                $('.close-alipay').click(function () {
                    $('.mask').hide();
                    $('.alipay-pop').animate({ 'bottom': '-100%' });
                    $('html').css('overflow', 'auto');
                })
            })
        }
    },
    error: function(){
        console.log('error');
    }
})
// 删除、取消、已服务
function btnEvent(info, url, id){
    layer.open({
        content: info,
        btn: ['确定', '取消'],
        yes: function(index){
            layer.close(index);
            $.ajax({
                url: url,
                dataType: 'JSON',
                type: 'POST',
                data: {
                    'order_id': id
                },
                success: function(res){
                    console.log(res);
                    if(res.status == 1){
                        location.reload();
                    }
                },
                error: function(){
                    console.log('error');
                }
            })
        }
    })
}
// 评价、查看详情
function detailAndEva(url, orderNum, link){
    $.ajax({
        url: url,
        dataType: 'JSON',
        type: 'POST',
        data: {
            'service_order_number': orderNum
        },
        success: function(res){
            console.log(res);
            if(res.status == 1){
                location.href = link;
            }
        },
        error: function(){
            console.log('error');
        }
    })
}




