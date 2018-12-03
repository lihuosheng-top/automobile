$('.tabs button').click(function(){
    var $index = $(this).index();
    switch($index){
        case 0:
            location.href = 'order_parts_all';break;
        case 1:break;
        case 2:
            location.href = 'order_wait_deliver';break;
        case 3:
            location.href = 'order_wait_evaluate';break;
        case 4:
            location.href = 'order_parts_return_goods';break;
    }
})
$('.icon-back').click(function(){
    location.href = 'my_index';
})

$.ajax({
    url: 'ios_api_order_parts_wait_pay',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
    },
    error: function(){
        console.log('error');
    }
})