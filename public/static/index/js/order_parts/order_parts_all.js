
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
// 选择支付方式
$('.widway').click(function(){
    $('.alipay-pop').animate({'bottom': '-100%'});
    $('.chose-payment').animate({'bottom': '0'});
})
function backMethod(){
    $('.alipay-pop').animate({'bottom': '0'});
    $('.chose-payment').animate({'bottom': '-100%'});
}
$('.method-div').click(function(){
    $(this).siblings().find('.check-a').removeClass('icon-check');
    $(this).find('.check-a').addClass('icon-check');
    var paymentMethod = $(this).find('p').text();
    $('.widway').val(paymentMethod);
    backMethod();
})
$('.chose-payment-back').click(function(){
    backMethod();
})
