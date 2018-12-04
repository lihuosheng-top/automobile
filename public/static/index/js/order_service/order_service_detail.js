var app = new Vue({
    el: '#app',
    data: {
        cancelPopFlag: false,
        alipayPopFlag: false,
        maskPopFlag: false,
    },
    methods: {
        // 取消订单
        cancelOrder(){
            this.cancelPopFlag = !this.cancelPopFlag;
            this.maskPopFlag = !this.maskPopFlag;
        },
        // 支付弹窗
        alipayPopFn(){
            this.alipayPopFlag = !this.alipayPopFlag;
            this.maskPopFlag = !this.maskPopFlag;
        }
    }
})
