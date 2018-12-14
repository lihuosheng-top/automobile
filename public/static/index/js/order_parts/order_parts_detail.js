$.ajax({
    url: 'order_parts_detail',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        if(val.status === 1){
            statusTxt = `待付款`;
        }else if(val.status === 2 || val.status === 3 || val.status === 4 || val.status === 5){
            statusTxt = `待收货`;
        }else if(val.status === 6 || val.status === 7){
            statusTxt = `待评价`;
        }else if(val.status === 8){
            statusTxt = `已完成`;
        }else if(val.status === 9 || val.status === 10){
            statusTxt = `已取消`;
        }else if(val.status === 11){
            statusTxt = `退货`;
        }
    },
    error: function(){
        console.log('error');
    }
})