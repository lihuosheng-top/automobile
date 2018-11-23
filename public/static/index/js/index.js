// 模糊搜索
$(function(){
    // 搜索框
    var search_input = $('input[name="search_input"]');
    $(search_input).keyup(function(){
        // jquery contains方法， :contains(text) 先隐藏后显示
        $('.num_name:not(:contains('+search_input.val().trim()+'))').parents('.sort_list').add('.sort_letter').hide();
        $('.num_name:contains('+search_input.val().trim()+')').parents('.sort_list').show();
        // 首字母标签显示
        // $('.num_name:contains('+search_input.val().trim()+')').parents('.sort_list').prev('.sort_letter').show();
    });
})

// 显示添加爱车弹窗
$('.add_mycar a').click(function(){
    $('.select-car').show();
    $('.wrapper').hide();
})
// 显示 选择车系 弹窗


// 添加车首字母匹配 start
$(function(){
    var Initials=$('.initials');
    var LetterBox=$('#letter');
    Initials.find('ul').append('<li>A</li><li>B</li><li>C</li><li>D</li><li>E</li><li>F</li><li>G</li><li>H</li><li>I</li><li>J</li><li>K</li><li>L</li><li>M</li><li>N</li><li>O</li><li>P</li><li>Q</li><li>R</li><li>S</li><li>T</li><li>U</li><li>V</li><li>W</li><li>X</li><li>Y</li><li>Z</li><li>#</li>');
    initials();

    $(".initials ul li").click(function(){
        var _this=$(this);
        var LetterHtml=_this.html();
        LetterBox.html(LetterHtml).fadeIn();

        setTimeout(function(){
            LetterBox.fadeOut();
        },1000);

        var _index = _this.index();
        if(_index==0){
            $('.cont').animate({scrollTop: '0px'}, 300);//点击第一个滚到顶部
        }else if(_index==27){
            var DefaultTop=$('#default').position().top;
            $('.cont').animate({scrollTop: DefaultTop +'px'}, 300);//点击最后一个滚到#号
        }else{
            var letter = _this.text();
            if($('#'+letter).length>0){//点击的字母 有值
                var LetterTop = $('#'+letter).position().top;
                $('.cont').animate({scrollTop: LetterTop-45+'px'}, 300);
                // var bodyTop = $('.cont').position().top;
            }
            console.log(LetterTop);
        }
    })

    var windowHeight=$(window).height();
    var InitHeight=windowHeight-180;
    Initials.height(InitHeight);
    var LiHeight=InitHeight/28;
    Initials.find('li').height(LiHeight);
})
function initials() {//公众号排序
    var SortList=$(".sort_list");
    var SortBox=$(".sort_box");
    SortList.sort(asc_sort).appendTo('.sort_box');//按首字母排序
    function asc_sort(a, b) {
        return makePy($(b).find('.num_name').text().charAt(0))[0].toUpperCase() < makePy($(a).find('.num_name').text().charAt(0))[0].toUpperCase() ? 1 : -1;
    }

    var carInitials = [];
    var num=0;
    SortList.each(function(i) {
        var initial = makePy($(this).find('.num_name').text().charAt(0))[0].toUpperCase();
        if(initial>='A'&&initial<='Z'){
            if (carInitials.indexOf(initial) === -1)
                carInitials.push(initial);
        }else{
            num++;
        }
        
    });

    $.each(carInitials, function(index, value) {//添加首字母标签
        SortBox.append('<div class="sort_letter" id="'+ value +'">' + value + '</div>');
    });
    if(num!=0){SortBox.append('<div class="sort_letter" id="default">#</div>');}

    for (var i =0;i<SortList.length;i++) {//插入到对应的首字母后面
        var letter=makePy(SortList.eq(i).find('.num_name').text().charAt(0))[0].toUpperCase();
        switch(letter){
            case "A":
                $('#A').after(SortList.eq(i));
                break;
            case "B":
                $('#B').after(SortList.eq(i));
                break;
            case "C":
                $('#C').after(SortList.eq(i));
                break;
            case "D":
                $('#D').after(SortList.eq(i));
                break;
            case "E":
                $('#E').after(SortList.eq(i));
                break;
            case "F":
                $('#F').after(SortList.eq(i));
                break;
            case "G":
                $('#G').after(SortList.eq(i));
                break;
            case "H":
                $('#H').after(SortList.eq(i));
                break;
            case "I":
                $('#I').after(SortList.eq(i));
                break;
            case "J":
                $('#J').after(SortList.eq(i));
                break;
            case "K":
                $('#K').after(SortList.eq(i));
                break;
            case "L":
                $('#L').after(SortList.eq(i));
                break;
            case "M":
                $('#M').after(SortList.eq(i));
                break;
            case "O":
                $('#O').after(SortList.eq(i));
                break;
            case "P":
                $('#P').after(SortList.eq(i));
                break;
            case "Q":
                $('#Q').after(SortList.eq(i));
                break;
            case "R":
                $('#R').after(SortList.eq(i));
                break;
            case "S":
                $('#S').after(SortList.eq(i));
                break;
            case "T":
                $('#T').after(SortList.eq(i));
                break;
            case "U":
                $('#U').after(SortList.eq(i));
                break;
            case "V":
                $('#V').after(SortList.eq(i));
                break;
            case "W":
                $('#W').after(SortList.eq(i));
                break;
            case "X":
                $('#X').after(SortList.eq(i));
                break;
            case "Y":
                $('#Y').after(SortList.eq(i));
                break;
            case "Z":
                $('#Z').after(SortList.eq(i));
                break;
            default:
                $('#default').after(SortList.eq(i));
                break;
        }
    };
}
// 添加车首字母匹配 end

// 城市定位 首字母匹配 start
$(function(){
    var Initials=$('.gec-initials');
    var LetterBox=$('#gec-letter');
    Initials.find('ul').append('<li>A</li><li>B</li><li>C</li><li>D</li><li>E</li><li>F</li><li>G</li><li>H</li><li>I</li><li>J</li><li>K</li><li>L</li><li>M</li><li>N</li><li>O</li><li>P</li><li>Q</li><li>R</li><li>S</li><li>T</li><li>U</li><li>V</li><li>W</li><li>X</li><li>Y</li><li>Z</li>');
    gecInitials();

    $(".gec-initials ul li").click(function(){
        var _this=$(this);
        var LetterHtml=_this.html();
        LetterBox.html(LetterHtml).fadeIn();

        setTimeout(function(){
            LetterBox.fadeOut();
        },1000);

        var _index = _this.index();
        if(_index==0){
            $('.gec-cont').animate({scrollTop: '0px'}, 300);//点击第一个滚到顶部
        }else{
            var letter = _this.text();
            if($('#gec_'+letter).length>0){//点击的字母 有值
                var LetterTop = $('#gec_'+letter).position().top;
                $('.gec-cont').animate({scrollTop: LetterTop-45+'px'}, 300);
                // var bodyTop = $('.gec-cont').position().top;
            }
            console.log(LetterTop);
        }
    })

    var windowHeight=$(window).height();
    var InitHeight=windowHeight-180;
    Initials.height(InitHeight);
    var LiHeight=InitHeight/28;
    Initials.find('li').height(LiHeight);
})
function gecInitials() {//公众号排序
    var gecSortList=$(".gec-sort-list");
    var gecSortBox=$(".gec-sort-box");
    gecSortList.sort(asc_sort).appendTo('.gec-sort-box');//按首字母排序
    function asc_sort(a, b) {
        return makePy($(b).find('.gec-city-name').text().charAt(0))[0].toUpperCase() < makePy($(a).find('.gec-city-name').text().charAt(0))[0].toUpperCase() ? 1 : -1;
    }

    var initials = [];
    // var num=0;
    gecSortList.each(function(i) {
        var initial = makePy($(this).find('.gec-city-name').text().charAt(0))[0].toUpperCase();
        if(initial>='A'&&initial<='Z'){
            if (initials.indexOf(initial) === -1)
                initials.push(initial);
        }
        // else{
        //     num++;
        // }
        
    });

    $.each(initials, function(index, value) {//添加首字母标签
        gecSortBox.append('<div class="gec-sort-letter" id="gec_'+ value +'">' + value + '</div>');
    });
    // if(num!=0){SortBox.append('<div class="gec-sort-letter" id="gec-default">#</div>');}

    for (var i =0;i<gecSortList.length;i++) {//插入到对应的首字母后面
        var gecLetter=makePy(gecSortList.eq(i).find('.gec-city-name').text().charAt(0))[0].toUpperCase();
        switch(gecLetter){
            case "A":
                $('#gec_A').after(gecSortList.eq(i));
                break;
            case "B":
                $('#gec_B').after(gecSortList.eq(i));
                break;
            case "C":
                $('#gec_C').after(gecSortList.eq(i));
                break;
            case "D":
                $('#gec_D').after(gecSortList.eq(i));
                break;
            case "E":
                $('#gec_E').after(gecSortList.eq(i));
                break;
            case "F":
                $('#gec_F').after(gecSortList.eq(i));
                break;
            case "G":
                $('#gec_G').after(gecSortList.eq(i));
                break;
            case "H":
                $('#gec_H').after(gecSortList.eq(i));
                break;
            case "I":
                $('#gec_I').after(gecSortList.eq(i));
                break;
            case "J":
                $('#gec_J').after(gecSortList.eq(i));
                break;
            case "K":
                $('#gec_K').after(gecSortList.eq(i));
                break;
            case "L":
                $('#gec_L').after(gecSortList.eq(i));
                break;
            case "M":
                $('#gec_M').after(gecSortList.eq(i));
                break;
            case "O":
                $('#gec_O').after(gecSortList.eq(i));
                break;
            case "P":
                $('#gec_P').after(gecSortList.eq(i));
                break;
            case "Q":
                $('#gec_Q').after(gecSortList.eq(i));
                break;
            case "R":
                $('#gec_R').after(gecSortList.eq(i));
                break;
            case "S":
                $('#gec_S').after(gecSortList.eq(i));
                break;
            case "T":
                $('#gec_T').after(gecSortList.eq(i));
                break;
            case "U":
                $('#gec_U').after(gecSortList.eq(i));
                break;
            case "V":
                $('#gec_V').after(gecSortList.eq(i));
                break;
            case "W":
                $('#gec_W').after(gecSortList.eq(i));
                break;
            case "X":
                $('#gec_X').after(gecSortList.eq(i));
                break;
            case "Y":
                $('#gec_Y').after(gecSortList.eq(i));
                break;
            case "Z":
                $('#gec_Z').after(gecSortList.eq(i));
                break;
            default:
                $('#gec-default').after(gecSortList.eq(i));
                break;
        }
    };
}
// 城市定位 首字母匹配 end

// 添加爱车 返回
$('.add-back').click(function(){
    $('.select-car').hide();
    $('.wrapper').show();
})

var map = new AMap.Map('container', {
    zoom: 12, //级别
    center: [114.07, 22.62]
});
AMap.plugin([
    'AMap.CitySearch',
    'AMap.Geolocation',
], function () {
    var citySearch = new AMap.CitySearch()
    citySearch.getLocalCity(function (status, result) {
        if (status === 'complete' && result.info === 'OK') {
            // 查询成功，result即为当前所在城市信息
            $('.gec-curr-txt').text(result.city);
            $('.curr_city').text(result.city);
            // console.log(result);
        }
    })

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
        // console.log(e.addressComponent.city);
    };
    function onError(e){
        // console.log(e);
    };
})
// 城市定位 弹窗
$('.map').click(function(){
    $('.wrapper').hide();
    $('.geclocation-pop').show();
})
// 城市定位弹窗 返回
$('.gec-back').click(function(){
    $('.select-car').hide();
    $('.wrapper').show();
})