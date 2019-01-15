$.ajax({
    url: 'love_car',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log('汽车列表', res);
        var hotBrand = res.data.brand.slice(0, 10);
        var hotBrandStr = '';
        var carBrandStr = '';
        // 爱车热门品牌
        $.each(hotBrand, function(idx, val){
            hotBrandStr += `<li class="hot-brand-li">\
                                <img src="uploads/`+val.images+`">\
                                <span class="txt-hid-one">`+val.brand+`</span>\
                            </li>`;
        })
        $('.hot-brand-ul').append(hotBrandStr);
        // 爱车品牌
        $.each(res.data.brand, function(idx, val){
            carBrandStr += `<div class="sort_list">\
                                <div class="num_logo">\
                                    <img src="uploads/`+val.images+`">\
                                </div>\
                                <div class="num_name">`+val.brand+`</div>\
                            </div>`;
        })
        $('.sort_box').append(carBrandStr);
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
                    case "N":
                        $('#N').after(SortList.eq(i));
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

        // 点击品牌 选择车系 start
        // 用户选择的车名
        var userChoseCarName;
        $('.hot-brand-ul li').add('.sort_list').click(function(){
            $('.select-car').hide();
            $('.car-series').show();
            userChoseCarName = $(this)[0].innerText.trim();
            $('.car-brand').html(userChoseCarName);
            // 清空容器
            $('.series-list').html('');
            var vehicleStr = '';
            // 遍历品牌 筛选车系
            var vehicleArr = [];
            $.each(res.data.series, function(idx, val){
                if(userChoseCarName == val.brand){
                    // 筛选对应品牌车系 插入数组
                    vehicleArr.push(val.series);
                }
            })
            // 去重后车系
            var uniqVehicle = unique(vehicleArr);
            // 循环
            for(var i = 0, l = uniqVehicle.length; i < l; i++){
                vehicleStr += '<div class="series-txt">'+uniqVehicle[i]+'</div>';
            }
            $('.series-list').append(vehicleStr);

            // 点击车系 选择排量 start
            // 用户选择的车系
            var userChoseCarType;
            $('.series-txt').click(function(){
                $('.car-series').hide();
                $('.motorcycle-type').show();
                userChoseCarType = $(this)[0].innerText.trim();
                // 清空容器
                $('.type-list').html('');
                var motorcycleTypeStr = '';
                // 遍历车系 筛选排量
                var motorcycleArr = [];
                $.each(res.data.series, function(idx, val){
                    if(userChoseCarType == val.series){
                        motorcycleArr.push(val.displacement);
                    }
                })
                // 去重 排量
                var uniqMotorcycle = unique(motorcycleArr);
                // 循环
                for(var i = 0, l = uniqMotorcycle.length; i < l; i++){
                    motorcycleTypeStr += '<div class="type-txt">'+uniqMotorcycle[i]+'</div>'
                }
                $('.type-list').append(motorcycleTypeStr);

                // 点击车型 选择年产 start
                // 用户选择的排量
                var userChoseMoto;
                $('.type-txt').click(function(){
                    $('.motorcycle-type').hide();
                    $('.car-year').show();
                    userChoseMoto = $(this)[0].innerText.trim();
                    // 清空容器
                    $('.year-list').html('');
                    var yearStr = '';
                    // 遍历排量 筛选年产
                    var yearArr = [];
                    $.each(res.data.series, function(idx, val){
                        if(userChoseMoto == val.displacement){
                            yearArr.push(val.year);
                        }
                    })
                    // 去重 年产
                    var uniqYear = unique(yearArr);
                    // 循环
                    for(var i = 0, l = uniqYear.length; i < l; i++){
                        yearStr += '<div class="year-txt">'+uniqYear[i]+'</div>'
                    }
                    $('.year-list').append(yearStr);

                    // 添加我的爱车 传参 start
                    // 用户选择的年产
                    var userChoseYear;
                    $('.year-txt').click(function(){
                        userChoseYear = $(this)[0].innerText.trim();
                        $.ajax({
                            url: 'love_save',
                            type: 'POST',
                            dataType: 'JSON',
                            data: {
                                'brand': userChoseCarName,
                                'series': userChoseCarType,
                                'displacement': userChoseMoto,
                                'production_time': userChoseYear
                            },
                            success: function(data){
                                console.log(data);
                                if(data.status == 1){
                                    layer.open({
                                        skin: 'msg',
                                        content: data.info,
                                        time: .8
                                    })
                                    setTimeout(function(){
                                        location.href = 'love_list';
                                    }, 1000);
                                }else if(data.status == 2){
                                    layer.open({
                                        skin: 'msg',
                                        content: data.info,
                                        time: .8
                                    })
                                    setTimeout(function(){
                                        location.href = 'login';
                                    },1000)
                                }else if(data.status == 0){
                                    layer.open({
                                        skin: 'msg',
                                        content: data.info,
                                        time: .8
                                    })
                                }
                            },
                            error: function(){
                                console.log('error');
                            }
                        })
                    })
                    // 添加我的爱车 传参 end
                })
                $('.year-back').click(function(){
                    $('.motorcycle-type').show();
                    $('.car-year').hide();
                })
                // 点击排量 选择年产 end

            })
            $('.type-back').click(function(){
                $('.car-series').show();
                $('.motorcycle-type').hide();
            })
            // 点击车系 选择排量 end

        })
        $('.series-back').click(function(){
            $('.select-car').show();
            $('.car-series').hide();
        })
        // 点击品牌 选择排量 end
    },
    error: function(){
        console.log('error');
    }
})
// 去重
function unique(arr){
    arr.sort(); 
    var result = [arr[0]];
    for(var i = 1; i < arr.length; i++){
        if(arr[i] !== result[result.length - 1]){
            result.push(arr[i]);
        }
    }
    return result;
}

// 我的爱车显示
$.ajax({
    url: 'index',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log('爱车', res);
        if(res.status == 1){
            var res = res.data[0];
            $('.txt-div p').html(res.brand);
            $('.txt-div span').html(res.series + ' ' + res.displacement + ' ' + res.production_time);
        }
        // else{
        //     $('.service-container').on('click', 'li:eq(0)', function(e){
        //         e.preventDefault();
        //         layer.open({
        //             skin: 'msg',
        //             content: '请添加爱车',
        //             time: .8
        //         })
        //     })
        // }
    },
    error: function(){
        console.log('爱车显示：error')
    }
})

// 模糊搜索
$(function(){
    // 汽车搜索框
    var search_input = $('input[name="search_input"]');
    $(search_input).keyup(function(){
        // jquery contains方法， :contains(text) 先隐藏后显示
        $('.num_name:not(:contains('+search_input.val().trim()+'))').parents('.sort_list').add('.sort_letter').hide();
        $('.num_name:contains('+search_input.val().trim()+')').parents('.sort_list').show();
        // 首字母标签显示
        $('.num_name:contains('+search_input.val().trim()+')').parents('.sort_list').prev('.sort_letter').show();
    });
    // 城市 搜索框
    // var gecSearchInput = $('input[name="gec-search-input"]');
    // $(gecSearchInput).keyup(function(){
    //     // jquery contains方法， :contains(text) 先隐藏后显示
    //     $('.gec-city-name:not(:contains('+gecSearchInput.val().trim()+'))').parents('.gec-sort-list').add('.gec-sort-letter').hide();
    //     $('.gec-city-name:contains('+gecSearchInput.val().trim()+')').parents('.gec-sort-list').show();
    //     // 首字母标签显示
    //     $('.gec-city-name:contains('+gecSearchInput.val().trim()+')').parents('.gec-sort-list').prev('.gec-sort-letter').show();
    // });
})

// 显示添加爱车弹窗
$('.add_mycar a').click(function(){
    $('.select-car').show();
    $('.wrapper').hide();
})

// 添加爱车 返回
$('.add-back').click(function(){
    $('.select-car').hide();
    $('.wrapper').show();
})

// 城市定位 首字母匹配 start
// $(function(){
//     var Initials=$('.gec-initials');
//     var LetterBox=$('#gec-letter');
//     Initials.find('ul').append('<li>A</li><li>B</li><li>C</li><li>D</li><li>E</li><li>F</li><li>G</li><li>H</li><li>I</li><li>J</li><li>K</li><li>L</li><li>M</li><li>N</li><li>O</li><li>P</li><li>Q</li><li>R</li><li>S</li><li>T</li><li>U</li><li>V</li><li>W</li><li>X</li><li>Y</li><li>Z</li>');
//     gecInitials();

//     $(".gec-initials ul li").click(function(){
//         var _this=$(this);
//         var LetterHtml=_this.html();
//         LetterBox.html(LetterHtml).fadeIn();

//         setTimeout(function(){
//             LetterBox.fadeOut();
//         },1000);

//         var _index = _this.index();
//         if(_index==0){
//             $('.gec-cont').animate({scrollTop: '0px'}, 300);//点击第一个滚到顶部
//         }else{
//             var letter = _this.text();
//             if($('#gec_'+letter).length>0){//点击的字母 有值
//                 var LetterTop = $('#gec_'+letter).position().top;
//                 $('.gec-cont').animate({scrollTop: LetterTop-45+'px'}, 300);
//             }
//             console.log(LetterTop);
//         }
//     })

//     var windowHeight=$(window).height();
//     var InitHeight=windowHeight-180;
//     Initials.height(InitHeight);
//     var LiHeight=InitHeight/28;
//     Initials.find('li').height(LiHeight);
// })
// function gecInitials() {//公众号排序
//     var gecSortList=$(".gec-sort-list");
//     var gecSortBox=$(".gec-sort-box");
//     gecSortList.sort(asc_sort).appendTo('.gec-sort-box');//按首字母排序
//     function asc_sort(a, b) {
//         return makePy($(b).find('.gec-city-name').text().charAt(0))[0].toUpperCase() < makePy($(a).find('.gec-city-name').text().charAt(0))[0].toUpperCase() ? 1 : -1;
//     }

//     var initials = [];
//     gecSortList.each(function(i) {
//         var initial = makePy($(this).find('.gec-city-name').text().charAt(0))[0].toUpperCase();
//         if(initial>='A'&&initial<='Z'){
//             if (initials.indexOf(initial) === -1)
//                 initials.push(initial);
//         }
//     });

//     $.each(initials, function(index, value) {//添加首字母标签
//         gecSortBox.append('<div class="gec-sort-letter" id="gec_'+ value +'">' + value + '</div>');
//     });
//     for (var i =0;i<gecSortList.length;i++) {//插入到对应的首字母后面
//         var gecLetter=makePy(gecSortList.eq(i).find('.gec-city-name').text().charAt(0))[0].toUpperCase();
//         switch(gecLetter){
//             case "A":
//                 $('#gec_A').after(gecSortList.eq(i));
//                 break;
//             case "B":
//                 $('#gec_B').after(gecSortList.eq(i));
//                 break;
//             case "C":
//                 $('#gec_C').after(gecSortList.eq(i));
//                 break;
//             case "D":
//                 $('#gec_D').after(gecSortList.eq(i));
//                 break;
//             case "E":
//                 $('#gec_E').after(gecSortList.eq(i));
//                 break;
//             case "F":
//                 $('#gec_F').after(gecSortList.eq(i));
//                 break;
//             case "G":
//                 $('#gec_G').after(gecSortList.eq(i));
//                 break;
//             case "H":
//                 $('#gec_H').after(gecSortList.eq(i));
//                 break;
//             case "I":
//                 $('#gec_I').after(gecSortList.eq(i));
//                 break;
//             case "J":
//                 $('#gec_J').after(gecSortList.eq(i));
//                 break;
//             case "K":
//                 $('#gec_K').after(gecSortList.eq(i));
//                 break;
//             case "L":
//                 $('#gec_L').after(gecSortList.eq(i));
//                 break;
//             case "M":
//                 $('#gec_M').after(gecSortList.eq(i));
//                 break;
//             case "N":
//                 $('#gec_N').after(gecSortList.eq(i));
//                 break;
//             case "O":
//                 $('#gec_O').after(gecSortList.eq(i));
//                 break;
//             case "P":
//                 $('#gec_P').after(gecSortList.eq(i));
//                 break;
//             case "Q":
//                 $('#gec_Q').after(gecSortList.eq(i));
//                 break;
//             case "R":
//                 $('#gec_R').after(gecSortList.eq(i));
//                 break;
//             case "S":
//                 $('#gec_S').after(gecSortList.eq(i));
//                 break;
//             case "T":
//                 $('#gec_T').after(gecSortList.eq(i));
//                 break;
//             case "U":
//                 $('#gec_U').after(gecSortList.eq(i));
//                 break;
//             case "V":
//                 $('#gec_V').after(gecSortList.eq(i));
//                 break;
//             case "W":
//                 $('#gec_W').after(gecSortList.eq(i));
//                 break;
//             case "X":
//                 $('#gec_X').after(gecSortList.eq(i));
//                 break;
//             case "Y":
//                 $('#gec_Y').after(gecSortList.eq(i));
//                 break;
//             case "Z":
//                 $('#gec_Z').after(gecSortList.eq(i));
//                 break;
//             default:
//                 $('#gec-default').after(gecSortList.eq(i));
//                 break;
//         }
//     };
// }
// $('.curr_city').click(function(){
//     $('.geclocation-pop').show();
//     $('.wrapper').hide();
// })
// $('.gec-back').click(function(){
//     $('.geclocation-pop').hide();
//     $('.wrapper').show();
// })
// 城市定位 首字母匹配 end


// var map = new AMap.Map('container', {
//     zoom: 12, //级别
//     center: [114.07, 22.62]
// });
// var threeAdress;
// map.plugin([
//     'AMap.Geolocation',
//     'AMap.Geocoder',//逆地理编码
// ], function () {
//     var geolocation = new AMap.Geolocation({
//         enableHighAccuracy: true,
//         // timeout: 5000,
//         zoomToAccuracy: true,
//     })
//     map.addControl(geolocation);
//     geolocation.getCurrentPosition();
//     AMap.event.addListener(geolocation, 'complete', onComplete);
//     AMap.event.addListener(geolocation, 'error', onError);
//     function onComplete(e){
//         console.log(e)
//         // alert(JSON.stringify(e))
//         // $('.gec-curr-txt').text(e.addressComponent.city);
//         $('.curr_city').text(e.addressComponent.district);
        // threeAdress = e.addressComponent.province+','+e.addressComponent.city+','+e.addressComponent.district;
//         if (!getCookie('area')) {
//             getAdvertisment(threeAdress);
//         }
//         setCookie('area', threeAdress, 7); //保存地址到cookie，有效期7天
//     };
//     function onError(e){
//         // console.log(e)
//         // alert(JSON.stringify(e))
//     };
// })
//  //页面初始化时，如果帐号密码cookie存在则填充
// if (getCookie('area')) {
//     getAdvertisment(getCookie('area'));
// }

// 获取商家的信息，如果存在则是商家角色，不存在则为车主
$.ajax({
    url: 'select_role_get',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log('获取商家的信息，如果存在则是商家角色，不存在则为车主',res);
        $('.my').click(function(){
            if(res.status == 1){
                location.href = 'sell_my_index';
            }else{
                location.href = 'my_index';
            }
        })
    },
    error: function(){
        console.log('error');
    }
})

// 热门店铺
$.ajax({
    url: 'index_shop',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log('热门店铺',res);
        if(res.status == 1){
            var str = '';
            $.each(res.data, function(idx, val){
                str += `<li class="hot-item" id="`+val.id+`">
                            <div class="hot-headimg">
                                <img src="uploads/`+val.shop_images+`">
                            </div>
                            <div class="hotshop-info">
                                <p class="hot-name">`+val.shop_name+`</p>
                                <div class="star-time">
                                    <i class="spr star1 `+(val.shop_star > 0 && val.shop_star < 1 ? '' : 
                                                        (val.shop_star >= 1 && val.shop_star < 2 ? 'star2' :
                                                        (val.shop_star >= 2 && val.shop_star < 3 ? 'star3' : 
                                                        (val.shop_star > 3 && val.shop_star < 4 ? 'star4' : 'star5'))))+`"></i>
                                    <span>营业时间：`+val.shop_time+`</span>
                                </div>
                                <p class="txt-hid-two hotshop-detail">`+val.shop_address.split(',').join('')+`</p>
                            </div>
                        </li>`
            })
            $('.hot-ul').append(str);
            $('.hot-item').click(function(){
                var id = $(this).attr('id');
                intoHotShop(id);
            })
        }
    },
    error: function(){
        console.log('error');
    }
})
// 进入 热门店铺
function intoHotShop(id){
    $.ajax({
        url: 'index_shop_goods',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': id
        },
        success: function(res){
            console.log(res);
            location.href = 'reservation_detail?store_id='+id;
        },
        error: function(){
            console.log('error');
        }
    })
}
// swiper初始化 
function mySwiper(){
    var mySwiper = new Swiper ('.swiper-container', {
        direction: 'horizontal', // 垂直切换选项
        autoplay: true, //自动播放
        delay: 3000,
        // 禁止滑动添加类名swiper-no-swiping
        // 如果需要分页器
        pagination: {
            el: '.swiper-pagination',
        },
        // 滑动后 切换也不停止
        autoplay: {
            disableOnInteraction: false
        }
    })
}
