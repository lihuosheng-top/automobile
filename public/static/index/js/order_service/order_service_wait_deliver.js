$('.tabs button').click(function(){
    var $index = $(this).index();
    switch($index){
        case 0:
            location.href = 'order_service_all';break;
        case 1:
            location.href = 'order_service_wait_pay';break;
        case 2:break;
        case 3:
            location.href = 'order_service_wait_evaluate';break;
        case 4:
            location.href = 'order_service_return_goods';break;
    }
})

$.ajax({
    url: 'ios_api_order_service_wait_deliver',
    dataType: 'JSON',
    type: 'POST',
    success: function(res){
        console.log(res);
        if(res.status == 1){
            var str = '';
            $.each(res.data, function(idx, val){
                str += `<div class="reservation-tab" id="`+val.id+`">
                            <div class="reservation-info-container">
                                <div class="order-num-status">
                                <div class="order-num">
                                    <i class="spr icon-order-num"></i>订单编号：<span>`+val.service_order_number+`</span>
                                </div>
                                <span class="status">待服务</span>
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
                            </div>
                        </div>`
            })
            $('.reservation-tab-container').append(str);
             // 查看详情
             $('.reservation-info-container').click(function(){
                var orderNum = $(this).parents('.reservation-tab').find('.order-num span').text();
                detailAndEva('order_service_save_record', orderNum, 'order_service_detail?page=3');
            })
            // 已服务 
            // $('.conf-receipt-btn').click(function(){
            //     var id = $(this).parents('.reservation-tab').attr('id');
            //     btnEvent('服务已完成？', 'ios_api_order_service_already_served', id);
            // })
        }
    },
    error: function(){
        console.log();
    }
})

// function btnEvent(info, url, id){
//     layer.open({
//         content: info,
//         btn: ['确定', '取消'],
//         yes: function(index){
//             layer.close(index);
//             $.ajax({
//                 url: url,
//                 dataType: 'JSON',
//                 type: 'POST',
//                 data: {
//                     'order_id': id
//                 },
//                 success: function(res){
//                     console.log(res);
//                     if(res.status == 1){
//                         location.reload();
//                     }
//                 },
//                 error: function(){
//                     console.log('error');
//                 }
//             })
//         }
//     })
// }
// 查看详情
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
