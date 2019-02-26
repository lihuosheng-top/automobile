// 获取url地址id
var url = location.search;
var service_setting_id;
if(url.indexOf('?') != -1){
    service_setting_id = url.substr(1).split('=')[1];
}
var userLngLat = [];//用户定位

var map = new AMap.Map('container', {
    zoom: 12, //级别
});
AMap.plugin([
    'AMap.Scale',
    'AMap.ToolBar',
    'AMap.Geolocation',
], function(){
    // 在图面添加比例尺控件，展示地图在当前层级和纬度下的比例尺
    map.addControl(new AMap.Scale());
    // 在图面添加工具条控件，工具条控件集成了缩放、平移、定位等功能按钮在内的组合控件
    map.addControl(new AMap.ToolBar());
    var geolocation = new AMap.Geolocation({
        enableHighAccuracy: true,
        timeout: 100,
        buttonPosition: 'RB',
        buttonOffset: new AMap.Pixel(10, 20),
        zoomToAccuracy: true
    })
    map.addControl(geolocation);
    geolocation.getCurrentPosition();
    AMap.event.addListener(geolocation, 'complete', onComplete);
    AMap.event.addListener(geolocation, 'error', onError);
    function onComplete(e){
        console.log(e);
        userLngLat = [e.position.lng, e.position.lat];
        if(e.status === 1){
            $('.shop_list').empty();
            showShops(e.formattedAddress);
        }
    };
    function onError(e){
        console.log(e);
    };
})
function showShops(addr){
    var markerList = [];//店铺经纬度
    // console.log(addr); 
    $.ajax({
        url: 'reservation',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'service_setting_id': service_setting_id,
            'store_user_address': addr
        },
        success: function(data){
            console.log(data);
            // 店铺距离 数组
            var dis = 0;
            var disArr = [];
            $.each(data.data, function(idx, val){
                // 店铺匹配
                if(val.serve_name !== undefined){
                    if(val.serve_name.longitude !== null && val.serve_name.latitude !== null){
                        var markerLngLat = [parseFloat(val.serve_name.longitude),parseFloat(val.serve_name.latitude)];
                        // 定位成功 计算用户与店铺距离
                        if(userLngLat.length !== 0){
                            dis = parseInt(AMap.GeometryUtil.distance(markerLngLat, userLngLat));
                        }
                        // 距离小于100公里
                        if(dis <= 100000){
                            // 存储距离
                            disArr.push({
                                id: val.id,
                                dis: (dis / 1000).toFixed(2)
                            })
                            // 存储标记点
                            markerList.push(new AMap.Marker({
                                position: new AMap.LngLat(parseFloat(val.serve_name.longitude),parseFloat(val.serve_name.latitude)),
                                title: val.serve_name.real_address,
                                offset: new AMap.Pixel(-20, -62),
                            }));
                        }
                    }
                }
            })
            // 从小到大排序
            var sortDisArr = disArr.sort(sortNum);
            (function(sortDisArr, data){
                var curPage = 0;
                // 页面滚动到底部 加载新的店铺
                $(document).ready(function() {
                    $(window).scroll(function() {
                        if ($(document).scrollTop() >= $(document).height() - $(window).height()) {
                            // 滚动底部 页数+1
                            ++curPage;
                            var len = Math.ceil( sortDisArr.length / 10 );
                            if(curPage < len){
                                renderPage(sortDisArr, data);
                            }
                        }
                    });
                });
                function renderPage(sortDisArr, data){
                    var str = '';
                    var len = (sortDisArr.length - curPage * 10) >= 10 ? 10 : sortDisArr.length - curPage * 10;
                    // 循环距离数组 一次10条
                    for(var i = 0; i < len; i++){
                        $.each(data.data, function(idx, ele){
                            if(ele.serve_name !== undefined){
                                if(ele.serve_name.longitude !== null && ele.serve_name.latitude !== null){
                                    if(sortDisArr[i + curPage * 10].id == ele.id){
                                        str += '<div>\
                                                    <a href="reservation_detail?store_id='+ele.store_id+'&service_setting_id='+service_setting_id+'" class="shop_box">\
                                                        <div class="shop-headimg"><img src=""></div>\
                                                        <div class="addr_info_box">\
                                                            <p class="shop_name_p">'+ele.serve_name.store_name+'</p>\
                                                            <div class="comment_box">\
                                                                <i class="spr icon_star"></i>\
                                                                <p class="statistic_member">'+ele.service_setting_name+'<span class="member_num">2000</span>人去过</p>\
                                                            </div>\
                                                            <p class="distance_addr_box">\
                                                                <span class="distance_span">约'+sortDisArr[i + curPage * 10].dis+'千米</span>\
                                                                <span class="addr_span">'+ele.serve_name.store_detailed_address+'</span>\
                                                            </p>\
                                                        </div>\
                                                        <div class="service_type">\
                                                            <p class="service_price">￥'+(ele.service_money !== null ? ele.service_money:'面议')+'</p>\
                                                            <p class="service_text">'+ele.service_setting_name+'</p>\
                                                        </div>\
                                                    </a>\
                                                </div>'
                                    }
                                }
                            }
                        })
                    }
                    $('.shop_list').append(str);
                }
                renderPage(sortDisArr, data);

            })(sortDisArr, data)

            map.add(markerList);
        },
        error: function(){
            console.log('error');
        },

    });
}
// 排位距离
function sortNum(a, b){
    return a.dis - b.dis;
}