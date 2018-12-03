
var app = new Vue({
    el: '#app',
    
})



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
        case 4:break;
    }
})

