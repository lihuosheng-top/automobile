var app = new Vue({
    el: '#app',
    data: {
        allDatas: ''
    },
    created: function(){
        this.getData()
    },
    methods: {
        getData: function(){
            this.$http.post('ios_api_order_parts_all')
            .then((res) => {
                console.log(eval("(" + res.body + ")"));
                this.allDatas = eval("(" + res.body + ")").data;
            }, (res) => {
                console.log('error');
            })
        }
    },
    computed: {
        
    }
})