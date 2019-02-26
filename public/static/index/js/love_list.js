// var calendar = new LCalendar();
// calendar.init({
//     'trigger': '#date',//标签id
//     'type': 'date',//date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择
//     'minDate':'2010-1-1',//最小日期 注意：该值会覆盖标签内定义的日期范围
//     'maxDate':'2050-12-31'//最大日期 注意：该值会覆盖标签内定义的日期范围
// });

$.ajax({
    url: 'love_list',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        var str = '';
        $.each(res.data, function(idx, val){
            if(val.status !== 1){
                str += '<div class="car-box">\
                            <div class="car-info-top" id="'+val.id+'">\
                                <div class="logo">'+(val.images ==null ? '<img src="static/index/image/aodi.png">' : '<img src="uploads/'+val.images.brand_images+'">')+'</div>\
                                <div class="car-info">\
                                    <p>'+val.brand+' '+val.series+'</p>\
                                    <p>'+val.displacement+' '+val.production_time+'</p>\
                                </div>\
                            </div>\
                            <div class="car-btn-bot">\
                                <button class="set-default" id="'+val.id+'">设为默认</button>\
                                <button class="delete-btn">删除</button>\
                            </div>\
                        </div>'
            }else{
                str += '<div class="car-box">\
                            <div class="car-info-top" id="'+val.id+'">\
                                <div class="logo">'+(val.images ==null ? '<img src="static/index/image/aodi.png">' : '<img src="uploads/'+val.images.brand_images+'">')+'</div>\
                                <div class="car-info">\
                                    <p>'+val.brand+' '+val.series+'</p>\
                                    <p>'+val.displacement+' '+val.production_time+'</p>\
                                </div>\
                            </div>\
                            <div class="car-btn-bot">\
                                <button class="set-default on-default" id="'+val.id+'">设为默认</button>\
                                <button class="delete-btn">删除</button>\
                            </div>\
                        </div>'
            }
        })
        $('.box-wrap').append(str);
        // 设为默认
        $('.set-default').click(function(){
            var id = $(this)[0].id;
            $('.box-wrap').find('.set-default').removeClass('on-default');
            $(this).addClass('on-default');
            $.ajax({
                url: 'love_status',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'id': id,
                    'status': 1
                },
                success: function(res){
                    console.log(res);
                    if(res.status === 1){
                        layer.open({
                            skin: 'msg',
                            content: res.info,
                            time: .8
                        })
                    }else{
                        layer.open({
                            skin: 'msg',
                            content: res.info,
                            time: .8
                        })
                    }
                },
                error: function(){
                    console.log('error');
                }
            })
        })
        // 删除
        $('.delete-btn').click(function(){
            var id = $(this).siblings()[0].id;
            $.ajax({
                url: 'love_del',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'id': id
                },
                success: function(res){
                    console.log(res);
                    if(res.status == 1){
                        layer.open({
                            style: 'bottom:100px;',
                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                            skin: 'msg',
                            content: res.info,
                            time: .8
                        })
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    }else{
                        layer.open({
                            style: 'bottom:100px;',
                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                            skin: 'msg',
                            content: res.info,
                            time: .8
                        })
                    }
                },
                error: function(){
                    console.log('error');
                }
            })
        })
        // 查看详细信息
        $('.car-info-top').click(function(){
            var id = $(this).attr('id');
            $.ajax({
                url: 'love_car_go',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    id: id
                },
                success: function(res){
                    console.log(res);
                    if(res.status == 1){
                        location.href = 'love_edit';
                    }
                },
                error: function(res){
                    console.log(res.status, res.statusText);
                }
            })

        })
        $('.add-car-btn').click(function(){
            location.href = 'index';
        })
    },
    error: function(){
        console.log('error');
    }
})


