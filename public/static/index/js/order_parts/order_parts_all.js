
var app = new Vue({
    el: '#app',
    data: {
        allDatas: '',
        allStatus: [],
    },
    created(){
        this.getData();
    },
    computed: {
        returnStatusTxt(){

        }
    },
    methods: {
        getData(){
            this.$http.post('ios_api_order_parts_all')
            .then(res => {
                this.allDatas = eval("(" + res.body + ")").data;
                console.log(eval("(" + res.body + ")").data);
                this.getStatus();
            }).catch(res => {
                console.error('error');
            })
        },
        getStatus(){
            this.allDatas.forEach(element => {
                this.allStatus.push(element.status);
            })
            // console.log(this.allStatus);
        }
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

