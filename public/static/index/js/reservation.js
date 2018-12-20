// 获取url地址id
var url = location.search;
var service_setting_id;
if(url.indexOf('?') != -1){
    service_setting_id = url.substr(1).split('=')[1];
}
var markerList = [];//店铺经纬度
$.ajax({
    url: 'reservation',
    type: 'POST',
    dataType: 'JSON',
    data: {
        'service_setting_id': service_setting_id,
    },
    success: function(data){
        console.log(data);
        var str = '';
        $.each(data.data, function(idx, val){
            if(val.store !== null){
                if(val.service_money !== null){
                    str += '<div>\
                                <a href="reservation_detail?store_id='+val.store_id+'&service_setting_id='+service_setting_id+'" class="shop_box">\
                                    <div class="addr_info_box">\
                                        <p class="shop_name_p">'+val.serve_name.store_name+'</p>\
                                        <div class="comment_box">\
                                            <i class="spr icon_star"></i>\
                                            <p class="statistic_member">汽车维修<span class="member_num">2000</span>人去过</p>\
                                        </div>\
                                        <p class="distance_addr_box">\
                                            <span class="distance_span">500米</span>\
                                            <span class="addr_span">'+val.serve_name.store_detailed_address+'</span>\
                                        </p>\
                                    </div>\
                                    <div class="service_type">\
                                        <p class="service_price">￥'+val.service_money+'</p>\
                                        <p class="service_text">'+val.service_setting_name+'</p>\
                                    </div>\
                                </a>\
                            </div>'
                }else{
                    str += '<div>\
                            <a href="reservation_detail?store_id='+val.store_id+'&service_setting_id='+service_setting_id+'" class="shop_box">\
                                <div class="addr_info_box">\
                                    <p class="shop_name_p">'+val.serve_name.store_name+'</p>\
                                    <div class="comment_box">\
                                        <i class="spr icon_star"></i>\
                                        <p class="statistic_member">汽车维修<span class="member_num">2000</span>人去过</p>\
                                    </div>\
                                    <p class="distance_addr_box">\
                                        <span class="distance_span">500米</span>\
                                        <span class="addr_span">'+val.serve_name.store_detailed_address+'</span>\
                                    </p>\
                                </div>\
                                <div class="service_type">\
                                    <p class="service_price">面议</p>\
                                    <p class="service_text">'+val.service_setting_name+'</p>\
                                </div>\
                            </a>\
                        </div>'
                }
                // 店铺经纬度
                if(val.serve_name.longitude !== null && val.serve_name.latitude !== null){
                    markerList[idx] = new AMap.Marker({
                        position: new AMap.LngLat(val.serve_name.longitude,val.serve_name.latitude),
                        title: val.serve_name.store_detailed_address,
                        offset: new AMap.Pixel(-20, -62),
                    });
                }
            }
        })
        console.log(markerList);
        map.add(markerList);
        $('.shop_list').append(str);
    },
    error: function(){
        console.log('error');
    }
})

var map = new AMap.Map('container', {
    zoom: 12, //级别
    center: [114.07, 22.62]
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
        timeout: 1000,
        buttonPosition: 'RB',
        buttonOffset: new AMap.Pixel(10, 20),
        zoomToAccuracy: true
    })
    map.addControl(geolocation);
    geolocation.getCurrentPosition();
    AMap.event.addListener(geolocation, 'complete', onComplete);
    AMap.event.addListener(geolocation, 'error', onError);
    function onComplete(e){
        // console.log(e.addressComponent);
    };
    function onError(e){
        // console.log(e);
    };
})
// 洗车店
var marker4 = new AMap.Marker({
    position: new AMap.LngLat(106.928746,27.712133),
    title: '遵义市汇川区长青路4号 爱车澡堂靓车养护服务中心',
    offset: new AMap.Pixel(-20, -62),
});
// markerList.push(marker4);
// console.log(markerList)



