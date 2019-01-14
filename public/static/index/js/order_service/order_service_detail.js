$.ajax({
    url: 'order_service_detail',
    dataType: 'JSON',
    type: 'POST',
    success: function(res){
        console.log(res);
        var statusTxt = '';
        if(res.status == 1){
            var val = res.data;
            if(val.status === 0 || val.status === 9 || val.status === 10){
                statusTxt = '已关闭';
                $('.del-order-btn').show();
            }else if(val.status === 1){
                statusTxt = '待付款';
                $('.cancel-order-btn').add('.to-payment-btn').show();
            }else if(val.status === 2 || val.status === 3){
                statusTxt = '待服务';
                $('.conf-receipt-btn').show();
            }else if(val.status === 4 || val.status === 5){
                statusTxt = '待评价';
                $('.del-order-btn').add('.evaluation-btn').show();
            }else if(val.status === 6){
                statusTxt = '已完成';
            }
            // 状态值
            $('.status').text(statusTxt);
            // 订单编号
            $('.order-num span').text(val.service_order_number);
            $('.order-num span').prop('id', val.id);
            // 车主
            $('.user-name span').text(val.car_owner_name);
            // 电话号码
            $('.user-phone').text(val.car_owner_phone_number);
            // 车牌号
            $('.address-title span').text(val.car_number);
            // 车辆信息
            $('.car-info').text(val.car_information);
            // 店铺信息
            $('.shop-address').text(val.store_name);
            // 到店时间
            $('.service-time span').text(val.got_to_time);
            // 打电话
            $('.call-phone a').prop('href', 'call:'+val.car_owner_phone_number);
            // 服务项目名称
            $('.service-item-name').text(val.service_goods_name);
            // 服务项目价格 服务总价值服务总价值  积分抵扣 实付款
            if(val.service_real_pay !== 0){
                $('.service-item-price span').text(val.service_order_amount);
                $('.service-total-sum p span').text(val.service_order_amount);
                $('.discount-amount-span span').text(val.integral_deductible);
                $('.pay-amount p span').text(val.service_real_pay);
            }else{
                $('.service-item-price').text('面议');
                $('service-total-sum p').text('面议');
                $('.discount-amount-span').text('');
                $('.pay-amount p').text('面议');
            }
            // 下单时间
            $('.create-time span').text(timetrans(val.create_time));
            // 服务完成时间
            if(val.reserve_time !== null){
                $('.create-time').after('<p class="pay-time">服务时间：<span>'+timetrans(val.reserve_time)+'</span></p>')
            }

            // 取消订单
            $('.cancel-order-btn').click(function(){
                var id = $('.order-num span').attr('id');
                btnEvent('确认取消订单？', 'ios_api_order_service_no_pay_cancel', id);
            })
            // 已服务 
            $('.conf-receipt-btn').click(function(){
                var id = $('.order-num span').attr('id');
                btnEvent('服务已完成？', 'ios_api_order_service_already_served', id);
            })
            // 删除订单
            $('.del-order-btn').click(function(){
                var id = $('.order-num span').attr('id');
                btnEvent('确认删除？', 'service_del', id);
            })
            // 去评价
            $('.evaluation-btn').click(function(){
                var orderNum = $(".order-num span").text();
                detailAndEva('order_service_save_record', orderNum, 'service_evaluate_index');
            })
            // 去付款
            $('.to-payment-btn').click(function(){
                var totalAmount = val.service_real_pay;
                var outTradeNo = val.service_order_number;
                var subjuect = val.service_goods_name;
                var body = val.service_goods_name;
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
                        location.href = 'order_service_all';
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
// 转换时间戳
function timetrans(date){
    var date = new Date(date*1000);//如果date为13位不需要乘1000
    var Y = date.getFullYear() + '-';
    var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
    var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate()) + ' ';
    var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
    var m = (date.getMinutes() <10 ? '0' + date.getMinutes() : date.getMinutes());
    // var s = (date.getSeconds() <10 ? '0' + date.getSeconds() : date.getSeconds());
    return Y+M+D+h+m;
}
