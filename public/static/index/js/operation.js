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
		timeout: 100,
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
		var address = e.formattedAddress;
		if(e.status === 1){
			$('.addr_info').text(address);
		}
		var ClickAddress = true;
		$(".send_add").click(function () {
			if(ClickAddress == true){
                sendAddressEvent('rescue_index', address);
                ClickAddress = false;
                setInterval(function () {
                    ClickAddress = true;
                },60*1000);
			}else {
                layer.open({
                    content: '您的信息已发送请耐心等待',
                    skin: 'msg',
                    time: 1.5
                });

			}


		});
	};
	function onError(e) {
		// console.log(e);
	};
})

function sendAddressEvent(url, add){
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'JSON',
		data: {
			'address': add
		},
		success: function (data) {
			console.log(data);
			if(data.status == 1){
				layer.open({
					content: '发送成功', 
					skin: 'msg', 
					time: 1.5
				});
			}
		},
		error: function () {
			console.log('error');
		}
	})
}
