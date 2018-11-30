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
                        <div class="car-info-top">
                            <div class="logo"><img src="static/index/image/aodi.png"></div>
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
                        <div class="car-info-top">
                            <div class="logo"><img src="static/index/image/aodi.png"></div>
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
                            style: 'bottom:100px;',
                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                            skin: 'msg',
                            content: res.info,
                            time: 1.5
                        })
                    }else{
                        layer.open({
                            style: 'bottom:100px;',
                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                            skin: 'msg',
                            content: res.info,
                            time: 1.5
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
                    if(res.status === 1){
                        layer.open({
                            style: 'bottom:100px;',
                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                            skin: 'msg',
                            content: res.info,
                            time: 1.5
                        })
                        alert(111)
                        setTimeout(function(){
                            window.location.reload();
                        }, 2000);
                    }else{
                        layer.open({
                            style: 'bottom:100px;',
                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                            skin: 'msg',
                            content: res.info,
                            time: 1.5
                        })
                    }
                },
                error: function(){
                    console.log('error');
                }
            })
        })
    },
    error: function(){
        console.log('error');
    }
    
    
})

