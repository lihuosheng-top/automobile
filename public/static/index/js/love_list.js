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
        })
        $('.box-wrap').append(str);
        $('.set-default').click(function(){
            var id = $(this)[0].id;
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

