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
    },
    error: function(){
        console.log('error');
    }
    
    
})

$('.car-info-top').click(function(){

})

