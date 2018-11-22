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
        console.log(e.addressComponent);
    };
    function onError(e){
        console.log(e);
    };
})

// 洗车店
var marker1 = new AMap.Marker({
    position: new AMap.LngLat(106.928746,27.712133),
    title: '遵义市汇川区长青路4号 爱车澡堂靓车养护服务中心',
    offset: new AMap.Pixel(-20, -62),
});
var marker2 = new AMap.Marker({
    position: new AMap.LngLat(106.937436,27.716676),
    title: '遵义市汇川区宁波路127号 格莱美汽车美容俱乐部',
   offset: new AMap.Pixel(-20, -62),
});
var marker3 = new AMap.Marker({
    position: new AMap.LngLat(106.941267,27.704973),
    title: '遵义市汇川区贵阳路132号 好品名车养护',
   offset: new AMap.Pixel(-20, -62),
});
var marker4 = new AMap.Marker({
    position: new AMap.LngLat(106.952123,27.715335),
    title: '遵义市汇川区高桥镇风华苑B栋1号 老司机洗车场',
    offset: new AMap.Pixel(-20, -62),
});
// 修理店
var marker5 = new AMap.Marker({
    position: new AMap.LngLat(106.940745,27.679557),
    title: '遵义市红花岗区东联络西50米 遵义市赵四汽配汽修',
    offset: new AMap.Pixel(-20, -62),
});
var marker6 = new AMap.Marker({
    position: new AMap.LngLat(106.934960,27.681064),
    title: '遵义市红花岗区沿江路98号地税局背后艺苑旁 来客信汽车维修',
    offset: new AMap.Pixel(-20, -62),
});
var markerList = [marker1,marker2,marker3,marker4,marker5,marker6];
map.add(markerList);
