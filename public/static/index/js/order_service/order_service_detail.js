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
            }else if(val.status === 1){
                statusTxt = '待付款';
            }else if(val.status === 2 || val.status === 3){
                statusTxt = '待服务';
            }else if(val.status === 4 || val.status === 5){
                statusTxt = '待评价';
            }else if(val.status === 6){
                statusTxt = '已完成';
            }
            // 状态值
            $('.status').text(statusTxt);
            // 订单编号
            $('.order-num span').text(val.service_order_number);
            // 车主
            $('.user-name span').text(val.car_owner_name);
            // 电话号码
            $('.user-phone').text(val.car_owner_phone_number);
            // 车牌号
            $('.address-title span').text(val.car_number);
            // 车辆信息
            $('.car-info').text();
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
                $('.pay-amount p span').text(val.service_real_pay);
            }else{
                $('.service-item-price').text('面议');
                $('service-total-sum p').text('面议');
                $('.pay-amount p').text('面议');
            }
        }
    },
    error: function(){
        console.log('error');
    }
})