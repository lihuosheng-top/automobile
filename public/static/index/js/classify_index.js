
// 分类中的品牌分类和商品分类
$.ajax({
    url: 'classify_index',
    type: 'POST',
    dataType: 'JSON',
    async: false,
    success: function(res){
        console.log(res);
        var hrefId = 1,//锚链接id
            brandLi = '',//左边品牌li
            goodsLi = '';
        $.each(res.data.goods_brand, function(idx, val){//循环品牌
            brandLi += '<li><a href="#'+hrefId+'">'+val.name+'</a></li>';

            if(val.brand_describe == '轮胎品牌区'){
                goodsLi += '<li class="first_li tyre" id="1">\
                                <div class="adop-box">\
                                    <p class="subtitle" id="'+hrefId+'">轮胎品牌区</p>\
                                    <span class="click-adop">点击适配</span>\
                                </div>\
                                <ul class="bgw">';
            }else{
                goodsLi += '<li class="first_li maintain"">\
                                <p class="subtitle" id="'+hrefId+'">'+val.brand_describe+'</p>\
                                <ul class="bgw">';
            }
            // 循环次级商品
            if(val.child.length != 0){
                $.each(val.child, function(idx, val){
                    goodsLi += '<li>\
                                    <a href="goods_list?id='+val.id+'">\
                                        <img src="uploads/'+val.brand_images+'">\
                                        <span class="brand_name">'+val.name+'</span>\
                                    </a>\
                                </li>'
                })
                goodsLi += '</ul></li>';
            }
            hrefId+=1;
        })
        
        //循环商品
        $.each(res.data.goods_type, function(idx, val){
            brandLi += '<li><a href="#'+hrefId+'">'+val.name+'</a></li>';

            goodsLi += '<li class="first_li maintain"">\
                            <p class="subtitle" id="'+hrefId+'">'+val.name+'</p>\
                            <ul class="bgw">';
            // 循环次级商品
            if(val.child.length != 0){
                $.each(val.child, function(idx, val){
                    goodsLi += '<li>\
                                    <a href="goods_list?id='+val.id+'">\
                                        <img src="uploads/'+val.type_images+'">\
                                        <span class="brand_name">'+val.name+'</span>\
                                    </a>\
                                </li>'
                })
                goodsLi += '</ul></li>';
            }
            hrefId+=1;
        })

        $('.cont_one_ul').append(goodsLi);
        $('.classify_ul').append(brandLi);

        $('.classify_tab li').click(function(){
            $(this).addClass('active').siblings().removeClass('active');
        })
        $('.click-adop').click(function(){
            location.href = 'goods_list';
        })
    },
    error: function(){
        console.log('error');
    }
})