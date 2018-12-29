$.ajax({
    url: 'order_service_detail',
    dataType: 'JSON',
    type: 'POST',
    success: function(res){
        console.log(res);
    },
    error: function(){
        console.log('error');
    }
})