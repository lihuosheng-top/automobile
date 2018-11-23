$('.filter_box button').click(function(){
    $(this).addClass('current').siblings().removeClass('current');
    var idx = $(this).index();
})

// 获取url地址id
var url = location.search;
var id;
if(url.indexOf('?') != -1){
    id = url.substr(1).split('=')[1];
}
$.ajax({
    url: 'goods_list',
    type: 'POST',
    dataType: 'JSON',
    data: {
        'id': id
    },
    success: function(res){
        console.log(res);
    },
    error: function(){
        console.log('error');
    }
})



















