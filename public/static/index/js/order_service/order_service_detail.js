$.ajax({
    url: 'order_service_detail',
    dataType: 'JSON',
    type: 'POST',
    success: function(res){
        console.log(res);
        var statusTxt = '';
    },
    error: function(){
        console.log('error');
    }
})

// 转换时间戳
function timetrans(date){
    var date = new Date(date*1000);//如果date为13位不需要乘1000
    var Y = date.getFullYear() + '-';
    var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
    var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate()) + ' ';
    var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
    var m = (date.getMinutes() <10 ? '0' + date.getMinutes() : date.getMinutes());
    // var s = (date.getSeconds() <10 ? '0' + date.getSeconds() : date.getSeconds());
    return Y+M+D+h+m;
}
// 倒计时
function countDown(id, endTime, storeId, orderNum, reason){
    var nowDate = new Date();
    var endDate = new Date(endTime * 1000);
    // 相差总秒数
    var totalSecond = parseInt((endDate - nowDate) / 1000);
    if(totalSecond > 0){
        // console.log(totalSecond)
        // 小时
        var hours = Math.floor(totalSecond / 3600);
        // console.log(hours);
        // 分钟
        var minutes = Math.floor((totalSecond % 3600) / 60);
        // console.log(minutes);
        // 秒
        // var seconds = Math.floor((totalSecond % 3600) % 60);
        // console.log(seconds);
        document.getElementById(id).innerText = '剩'+hours+'小时'+minutes+'分自动关闭';
        setTimeout(function(){
            countDown(id, endTime, storeId, orderNum, reason);
        }, 1000)
    }else{
        $.ajax({
            url: 'order_parts_detail_cancel',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'store_id': storeId,
                'parts_order_number': orderNum,
                'cancel_order_description': reason
            },
            success: function(res){
                console.log(res);
                location.reload();
            },
            error: function(){
                console.log('error');
            }
        })
    }
}