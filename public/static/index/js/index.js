// 模糊搜索
// $(function(){
//     // 搜索框
//     var search_input = $('input[name="search_input"]');
//     $(search_input).keyup(function(){
//         // jquery contains方法， :contains(text) 先隐藏后显示
//         $('.user_info p:not(:contains('+search_input.val().trim()+'))').parents('.user_info').hide();
//         $('.user_info p:contains('+search_input.val().trim()+')').parents('.user_info').show();
//     });
// })

$(function(){
    var Initials=$('.initials');
    var LetterBox=$('#letter');
    Initials.find('ul').append('<li>A</li><li>B</li><li>C</li><li>D</li><li>E</li><li>F</li><li>G</li><li>H</li><li>I</li><li>J</li><li>K</li><li>L</li><li>M</li><li>N</li><li>O</li><li>P</li><li>Q</li><li>R</li><li>S</li><li>T</li><li>U</li><li>V</li><li>W</li><li>X</li><li>Y</li><li>Z</li><li>#</li>');
    // initials();

    $(".initials ul li").click(function(){
        var _this=$(this);
        var LetterHtml=_this.html();
        LetterBox.html(LetterHtml).fadeIn();

        setTimeout(function(){
            LetterBox.fadeOut();
        },1000);

        var _index = _this.index()
        if(_index==0){
            $('html,body').animate({scrollTop: '0px'}, 300);//点击第一个滚到顶部
        }else if(_index==27){
            var DefaultTop=$('#default').position().top;
            $('html,body').animate({scrollTop: DefaultTop+'px'}, 300);//点击最后一个滚到#号
        }else{
            var letter = _this.text();
            if($('#'+letter).length>0){//点击的字母 有值
                var LetterTop = $('#'+letter).position().top;
                console.log(LetterTop);
                $('html,body').animate({scrollTop: LetterTop-45+'px'}, 300);
                var bodyTop = $('html,body').position().top;
            }
        }
    })

    var windowHeight=$(window).height();
    var InitHeight=windowHeight-180;
    Initials.height(InitHeight);
    var LiHeight=InitHeight/28;
    Initials.find('li').height(LiHeight);
})