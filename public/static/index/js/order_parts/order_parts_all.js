
$.ajax({
    url: 'ios_api_order_parts_all',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
    },
    error: function(){
        console.log('err');
    }
})

// var app = new Vue({
//     el: '#app',
//     data: {
//         allDatas: '',
//     },
//     created(){
//         this.getData();
//     },
//     methods: {
//         getData(){
//             this.$http.post('ios_api_order_parts_all')
//             .then(res => {
//                 this.allDatas = eval("(" + res.body + ")").data;
//                 console.log(eval("(" + res.body + ")").data);
//             }).catch(res => {
//                 console.error('error');
//             })
//         },
//         returnStatusTxt(status){
//             if(status === 1){
//                 return `待付款`;
//             }else if(status === 2 || status === 3 || status === 4 || status === 5){
//                 return `待收货`;
//             }else if(status === 6 || status === 7){
//                 return `待评价`;
//             }else if(status === 8){
//                 return `已完成`;
//             }else if(status === 9 || status === 10){
//                 return `已取消`;
//             }else if(status === 11){
//                 return `退货`;
//             }
//         }
//     }
// })



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

