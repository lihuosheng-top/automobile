$('.verfy-li').click(function(){
    var $index = $(this).index();
    switch($index){
        case 0:
            $('.id-card-pop').css('right', '0');break;
        case 1:
            $('.business-license-pop').css('right', '0');break;
        case 2:
            $('.store-front-pop').css('right', '0');break;
        case 3:
            $('.address-pop').animate({'right': '0'});break;
    }
})
$('.id-card-back').add('.id-card-button').click(function(){
    $('.id-card-pop').css('right', '-100%');
})
$('.business-license-back').add('.business-license-button').click(function(){
    $('.business-license-pop').css('right', '-100%');
})
$('.store-front-back').add('.store-front-button').click(function(){
    $('.store-front-pop').css('right', '-100%');
})

// 上传图片 https://blog.csdn.net/scrat_kong/article/details/79230329
function uploadImg(input,img){
    var input = document.getElementById(input);
    var img = document.getElementById(img);
    var reader = new FileReader();//创建fileReader实例
    reader.readAsDataURL(input.files[0]);//发起异步请求
    reader.onload = function(){
        //读取完成后，数据保存在对象的result属性中
        img.src = this.result;
    }
}
$('#emblem-input').change(function(){
    uploadImg('emblem-input', 'emblem-img');
})
$('#portrait-input').change(function(){
    uploadImg('portrait-input', 'portrait-img');
})
$('#business-license-input').change(function(){
    uploadImg('business-license-input', 'business-license-img');
})
$('#license-input').change(function(){
    uploadImg('license-input', 'license-img');
})
$('#face-input').change(function(){
    uploadImg('face-input', 'face-img');
})

// 店内多图上传
var imagesFileArr = [];//存储多图files
$(function(){
    // 在浏览器上预览本地图片
    function getObjectURL(file) {
        var url = null;
        if(window.URL != undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file);
        } else if(window.webkitURL != undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file);
        }
        return url;
    }
    $('#inner-input').change(function(){
        var urlArr = [],
            str = '';
        var len =  this.files.length;
        // 限制上传20张图片
        if($('.store-inner-imgbox').length+len <= 20){

            for(var i = 0; i < len; i++){
                // 存 files
                imagesFileArr.push(this.files[i]);
                // 存图片地址
                urlArr.push(getObjectURL(this.files[i]));
            }
            console.log(imagesFileArr);

            $.each(urlArr, function(idx, val){
                str += `<div class="store-inner-imgbox">
                            <button class="close">×</button>
                            <img src="`+val+`">
                        </div>`
            })
            $('.mul-img').append(str);
        }else{
            layer.open({
                style: 'bottom:100px;',
                type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                skin: 'msg',
                content: '最多上传20张图片',
                time: 2
            })
        }
        // 删除图片
        $('.store-inner-imgbox').on('click', '.close', function(){
            var $index = $('.close').index($(this));
            console.log($index);
            imagesFileArr.splice($index, 1);
            console.log(imagesFileArr)
            $(this).parent().remove();
        })
    })
});

// 提交申请
$('.submit-button').click(function(){
    var emblemInput = $('#emblem-input')[0].files,
        portraitInput = $('#portrait-input')[0].files,
        businessLicense = $('#portrait-input')[0].files,
        license = $('#portrait-input')[0].files,
        faceInput = $('#portrait-input')[0].files,
        innerInput = imagesFileArr.length;
    // 经纬度  地址
    var address = $('#address').text();
    var lnglat = $('#lnglat').text();
    var formData = new FormData();
    formData.append('address', address);
    formData.append('lnglat', lnglat);
    if($(this).text() === '提交申请'){
        if(emblemInput.length !== 0 && portraitInput.length !== 0 && businessLicense.length !== 0 
            && license.length !== 0 && faceInput.length !== 0 && innerInput !== 0){
            formData.append('store_identity_card', emblemInput[0]);
            formData.append('store_reverse_images', portraitInput[0]);
            formData.append('store_do_bussiness_positive_img', businessLicense[0]);
            formData.append('store_do_bussiness_side_img', license[0]);
            formData.append('verifying_physical_storefront_one', faceInput[0]);
            $.each(imagesFileArr, function(idx, val){
                formData.append('verifying_physical_storefront_two[]', val);
            })
            $.ajax({
                url: 'store_update',
                type: 'POST',
                dataType: 'JSON',
                processData: false,
                contentType: false,
                data: formData,
                success: function(res){
                    console.log(res);
                    if(res.status == 1){
                        layer.open({
                            style: 'bottom:100px;',
                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                            skin: 'msg',
                            content: res.info,
                            time: 1.2
                        })
                        setTimeout(function(){
                            // location.href = 'my_index';
                        }, 1400);
                    }else{
                        layer.open({
                            style: 'bottom:100px;',
                            type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                            skin: 'msg',
                            content: res.info,
                            time: 1.2
                        })
                    }
                },
                error: function(){
                    console.log('error');
                }
            }) 
        }else{
            layer.open({
                style: 'bottom:100px;',
                type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                skin: 'msg',
                content: '图片未上传完整',
                time: 1
            })
        }
    }else if($(this).text() === '提交修改'){
        if(emblemInput.length !== 0){
            formData.append('store_identity_card', emblemInput[0]);
        }
        if(portraitInput.length !== 0){
            formData.append('store_reverse_images', portraitInput[0]);
        }
        if(businessLicense.length !== 0){
            console.log(businessLicense[0])
            formData.append('store_do_bussiness_positive_img', businessLicense[0]);
        }
        if(license.length !== 0){
            formData.append('store_do_bussiness_side_img', license[0]);
        }
        if(faceInput.length !== 0){
            formData.append('verifying_physical_storefront_one', faceInput[0]);
        }
        if(innerInput !== 0){
            $.each(imagesFileArr, function(idx, val){
                formData.append('verifying_physical_storefront_two[]', val);
            })
        }
        $.ajax({
            url: 'store_update',
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            data: formData,
            success: function(res){
                console.log(res);
                if(res.status == 1){
                    layer.open({
                        style: 'bottom:100px;',
                        type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                        skin: 'msg',
                        content: res.info,
                        time: 1.2
                    })
                    setTimeout(function(){
                        // location.href = 'my_index';
                    }, 1400);
                }else{
                    layer.open({
                        style: 'bottom:100px;',
                        type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                        skin: 'msg',
                        content: res.info,
                        time: 1.2
                    })
                }
            },
            error: function(){
                console.log('error');
            }
        }) 
    }
})
var updateImages = [];
// 返回数据
$.ajax({
    url: 'return_store_information',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        var data = res.data;
        if(data.store_identity_card !== null && data.store_reverse_images !== null &&
            data.store_do_bussiness_positive_img !== null && data.store_do_bussiness_side_img !== null &&
            data.verifying_physical_storefront_one !== null){
            var str = '';
            updateImages = data.imgs;
            $('#emblem-img')[0].src = 'uploads/' + data.store_identity_card;
            $('#portrait-img')[0].src =  'uploads/' + data.store_reverse_images;
            $('#business-license-img')[0].src = 'uploads/' + data.store_do_bussiness_positive_img;
            $('#license-img')[0].src = 'uploads/' + data.store_do_bussiness_side_img;
            $('#face-img')[0].src = 'uploads/' + data.verifying_physical_storefront_one;
            $.each(data.imgs, function(idx, val){
                str += `<div class="store-inner-imgbox">
                            <button class="close">×</button>
                            <img src="uploads/`+val+`">
                        </div>`
            })
            $('.mul-img').append(str);
            $('.submit-button').text('提交修改');
            // 删除图片
            $('.store-inner-imgbox').on('click', '.close', function(){
                var $index = $('.close').index($(this));
                console.log($index);
                var userDel = updateImages.slice($index, $index+1)[0];
                console.log(userDel);
                $(this).parent().remove();
                $.ajax({
                    url: 'url_img_del',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        'image_del': userDel
                    },
                    success: function(res){
                        console.log(res);
                    },
                    error: function(err){
                        console.error(err);
                    }
                })
            })
        }else{
            $('.submit-button').text('提交申请');
        }
    },
    error: function(err){
        console.error(err);
    }
})

AMapUI.loadUI(['misc/PositionPicker'], function(PositionPicker) {
    var map = new AMap.Map('container', {
        zoom: 16,
        scrollWheel: false
    })
    AMap.plugin([
        'AMap.ToolBar',
        'AMap.Geolocation'
    ], function(){
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
    })
    var positionPicker = new PositionPicker({
        mode: 'dragMap',
        map: map
    });
    positionPicker.on('success', function(positionResult) {
        document.getElementById('lnglat').innerHTML = positionResult.position;
        document.getElementById('address').innerHTML = positionResult.address;
    });
    positionPicker.on('fail', function(positionResult) {
        document.getElementById('lnglat').innerHTML = ' ';
        document.getElementById('address').innerHTML = ' ';
    });
    var onModeChange = function(e) {
        positionPicker.setMode(e.target.value)
    }
    positionPicker.start();
    map.panBy(0, 1);
});
$('.confirm-address').add('.icon-map-back').click(function(){
    $('.address-pop').animate({'right': '-100%'});
})
