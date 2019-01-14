var calendar = new LCalendar();
calendar.init({
    'trigger': '#date',//标签id
    'type': 'date',//date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择
    'minDate':'2010-1-1',//最小日期 注意：该值会覆盖标签内定义的日期范围
    'maxDate':'2050-12-31'//最大日期 注意：该值会覆盖标签内定义的日期范围
});

// 车辆信息 下拉框
let show = document.getElementById('show-input');
let words = document.getElementById('word-select');
show.onclick = function(e){
    if(words.style.display == 'none'){
        words.style.display = 'block';
    }else{
        words.style.display = 'none';
    }
}
words.size = 5;
words.onchange = function(e){
    var option = this.options[this.selectedIndex];
    show.value = option.innerHTML;
    words.style.display = 'none';
}

$.ajax({
    url: 'love_list',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        var str = '';
        $.each(res.data, function(idx, val){
            if(val.status !== 1){
                str += `<div class="car-box">
                            <div class="car-info-top" id="`+val.id+`">
                                <div class="logo">`+(val.images ==null ? '<img src="static/index/image/aodi.png">' : '<img src="uploads/'+val.images.brand_images+'">')+`</div>
                                <div class="car-info">
                                    <p>`+val.brand+` `+val.series+`</p>
                                    <p>`+val.displacement+` `+val.production_time+`</p>
                                </div>
                            </div>
                            <div class="car-btn-bot">
                                <button class="set-default" id="`+val.id+`">设为默认</button>
                                <button class="delete-btn">删除</button>
                            </div>
                        </div>`
            }else{
                str += `<div class="car-box">
                            <div class="car-info-top" id="`+val.id+`">
                                <div class="logo">`+(val.images ==null ? '<img src="static/index/image/aodi.png">' : '<img src="uploads/'+val.images.brand_images+'">')+`</div>
                                <div class="car-info">
                                    <p>`+val.brand+` `+val.series+`</p>
                                    <p>`+val.displacement+` `+val.production_time+`</p>
                                </div>
                            </div>
                            <div class="car-btn-bot">
                                <button class="set-default on-default" id="`+val.id+`">设为默认</button>
                                <button class="delete-btn">删除</button>
                            </div>
                        </div>`
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
            id = $(this).attr('id');
            var myCarInfo = '';
            $.each($(this).find('.car-info p'), function(idx, val){
                myCarInfo += val.innerText+' ';
            })
            console.log(myCarInfo)
            $('.wrapper').hide();
            $('.car-detail-pop').show();
            $.ajax({
                url: 'love_list_edit',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'id': id,
                },
                success: function(res){
                    console.log(res);
                    $('.info-p').text(myCarInfo);
                    if(res.status == 1){
                        var data = res.data[0];
                        $('.seat-input').val(data.seat);
                        $('.color-input').val(data.colour);
                        var selectWord = data.plate_number.split(' ')[0];
                        var selectNumber = data.plate_number.split(' ')[1];
                        $('.plate-num').text(selectWord+selectNumber);
                        $('.show-input').val(selectWord);
                        var selectEle = document.getElementById('word-select');
                        for(var i = 0, len = selectEle.options.length; i < len; i++){
                            if(selectEle.options[i].value == selectWord){
                                selectEle.options[i].selected = 'selected';
                            }
                        }
                        $('.plant-input').val(selectNumber);
                        $('.mileage-input').val(data.driving_number);
                        $('.vin-num-input').val(data.carriage_number);
                        $('.engine-no-input').val(data.engine_number);
                        $('.insurer-input').val(data.car_insurance);
                        $('.expiration-date-input').val(data.insurance_time);
                    }
                },
                error: function(){
                    console.log('error');
                }
            })
        })
        // 隐藏弹窗
        $('.detail-back').click(function(){
            $('.wrapper').show();
            $('.car-detail-pop').hide();
            location.reload();
        })
        $('.add-car-btn').click(function(){
            location.href = 'index';
        })
    },
    error: function(){
        console.log('error');
    }
})
// 保存车辆信息
var id;
$('.save-btn').click(function(){
    var seat = $('.seat-input').val();
    var colour = $('.color-input').val();
    var plate_number = $('#word-select option:selected').val()+' '+$('.plant-input').val();
    var driving_number = $('.mileage-input').val();
    var carriage_number = $('.vin-num-input').val();
    var engine_number = $('.engine-no-input').val();
    var car_insurance = $('.insurer-input').val();
    var insurance_time = $('.expiration-date-input').val();
    if($('.plant-input').val() !== ''){
        $.ajax({
            url: 'love_list_save',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'id': id,
                'seat': seat,
                'colour': colour,
                'plate_number': plate_number,
                'driving_number': driving_number,
                'carriage_number': carriage_number,
                'engine_number': engine_number,
                'car_insurance': car_insurance,
                'insurance_time': insurance_time
            },
            success: function(res){
                console.log(res);
                if(res.status == 1){
                    layer.open({
                        skin: 'msg',
                        content: res.info,
                        time: .8
                    })
                    setTimeout(function(){
                        location.reload();
                    }, 1000)
                    $('.wrapper').show();
                    $('.car-detail-pop').hide();
                }
            },
            error: function(){
                console.log('error');
            }
        })
    }else{
        layer.open({
            skin: 'msg',
            content: '请填写车牌号',
            time: 1
        })
    }
})

