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
// 倒计时
function countDown(id, endTime, storeId, orderNum, reason){
    var nowDate = new Date();
    var endDate = new Date(endTime * 1000);
    // 相差总秒数
    var totalSecond = parseInt((endDate - nowDate) / 1000);
    if(totalSecond > 0){
        // console.log(totalSecond)
        // 小时
        var hours = Math.floor(totalSecond / 3600);
        // console.log(hours);
        // 分钟
        var minutes = Math.floor((totalSecond % 3600) / 60);
        // console.log(minutes);
        // 秒
        // var seconds = Math.floor((totalSecond % 3600) % 60);
        // console.log(seconds);
        document.getElementById(id).innerText = '剩'+hours+'小时'+minutes+'分自动关闭';
        setTimeout(function(){
            countDown(id, endTime, storeId, orderNum, reason);
        }, 1000)
    }else{
        $.ajax({
            url: 'order_parts_detail_cancel',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'store_id': storeId,
                'parts_order_number': orderNum,
                'cancel_order_description': reason
            },
            success: function(res){
                console.log(res);
            },
            error: function(){
                console.log('error');
            }
        })
    }
}

$.ajax({
    url: 'order_parts_detail',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        var val = res.data;
        var statusTxt = '';
        if(val.status === 1){
            statusTxt = `待付款`;
            $('.cancel-order-btn').add('.to-payment-btn').show();
            $('.time-count-down').show();
            countDown('time-count-down', val.normal_future_time, val.store_id, val.parts_order_number, '超过规定时间未付款，系统自动关闭订单');
        }else if(val.status === 2 || val.status === 3 || val.status === 4 || val.status === 5){
            statusTxt = `待收货`;
            $('.check-logistics-btn').add('.conf-receipt-btn').show();
        }else if(val.status === 6 || val.status === 7){
            statusTxt = `待评价`;
            $('.evaluation-btn').add('.del-order-btn').show();
        }else if(val.status === 8){
            statusTxt = `已完成`;
        }else if(val.status === 9 || val.status === 10){
            statusTxt = `已取消`;
            $('.del-order-btn').show();
        }else if(val.status === 11){
            statusTxt = `退货`;
            $('.del-order-btn').show();
        }
        // 状态值
        $('.status').text(statusTxt);
        // 订单编号
        $('.order-num span').text(val.parts_order_number);
        // 收货人
        $('.user-name span').text(val.harvester);
        // 电话
        $('.user-phone').text(val.harvest_phone_num);
        // 配送地址
        $('.address-txt').text(val.harvester_address);
        // 店铺名
        $('.order-shop-namp').text(val.store_name);
        // 订单商品
        var str = '';
        $.each(val.info, function(idx, val){
            str += `<div class="order-goods-detail">
                        <div class="order-goods-img">
                            <img src="uploads/`+val.goods_image+`">
                        </div>
                        <div class="order-info-box">
                            <p class="order-goods-p txt-hid-two">`+val.parts_goods_name+`</p>
                            <p class="order-selling-point txt-hid-two">`+val.goods_describe+`</p>
                            <div class="unit-price-quantity">
                                <p class="unit-price-p">￥`+val.goods_money+`</p>
                                <p class="quantity-p">×`+val.order_quantity+`</p>
                            </div>
                        </div>
                    </div>`
        })
        $('.order-shop-box').after(str);
        // 商品总额
        $('.total-box span').text(val.all_goods_pays);
        // 抵扣金额
        $('.discount-box span').text(val.integral_deductible);
        // 需付款
        $('.pay-amount-span span').text(val.all_order_real_pay);
        // 创建时间
        $('.create-time span').text(timetrans(val.create_time));
        // 支付时间
        if(val.pay_time !== null){
            $('.create-time').after('<p class="pay-time">支付时间：<span>'+timetrans(val.pay_time)+'</span></p>')
        }
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
    },
    error: function(){
        console.log('error');
    }
})