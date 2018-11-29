
var app = new Vue({
    el: '#app',
    data: {
        allDatas: '',
        allStatus: [],
    },
    created(){
        this.getData();
        // this.getStatus();
    },
    methods: {
        getData(){
            this.$http.post('ios_api_order_parts_all')
            .then(res => {
                this.allDatas = eval("(" + res.body + ")").data;
                console.log(eval("(" + res.body + ")"));
            }).catch(res => {
                console.error('error');
            })
        },
        // getStatus(){
        //     this.allDatas.forEach(function(val){
        //         console.log(val)
        //     })
        // }
    }
})



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
$('.icon-back').click(function(){
    location.href = 'my_index';
})

// $.ajax({
//     url: 'ios_api_order_parts_all',
//     type: 'POST',
//     dataType: 'JSON',
//     success: function(res){
//         console.log(res);
//         $.each(res.data, function(idx, val){
//             // console.log(val);
//             $.each(val, function(idx, val){
//                 console.log(val)
//             })
//         })
//     },
//     error: function(){
//         console.log('error');
//     }
// })
