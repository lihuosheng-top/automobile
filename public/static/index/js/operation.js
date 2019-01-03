var map = new AMap.Map('container', {
	zoom: 12, //级别
	center: [114.07, 22.62]
});

AMap.plugin([
	'AMap.Scale',
	'AMap.ToolBar',
	'AMap.Geolocation',
], function () {
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
	function onComplete(e) {
		console.log(e);
		if(e.status === 1){
			$('.addr_info').text(e.formattedAddress);
		}
	};
	function onError(e) {
		// console.log(e);
	};
})
$(".send_add").click(function () {
	$.ajax({
		url: 'rescue_index',
		type: 'POST',
		dataType: 'JSON',
		data: {
			'address': add
		},
		success: function (data) {
			layer.open({
				content: '发送成功', 
				skin: 'msg', 
				time: 1.2
			});
			console.log(data);
		},
		error: function () {
			console.log('error');
		}
	})

});
