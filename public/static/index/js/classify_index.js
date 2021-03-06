var myHash = null;
// 分类中的品牌分类和商品分类
$.ajax({
    url: 'classify_index',
    type: 'POST',
    dataType: 'JSON',
    success: function(res){
        console.log(res);
        var hrefId = 1,//锚链接id
            brandLi = '',//左边品牌li
            goodsLi = '';
        // 如果是从首页是配件商城进来
        console.log(myHash);
        $.each(res.data.goods_brand, function(idx, val){//循环品牌
            if(idx === 0){
                brandLi += '<li class="active"><a href="#'+val.id+'">'+val.name+'</a></li>';
            }else{
                brandLi += '<li><a href="#'+val.id+'">'+val.name+'</a></li>';
            }

            if(val.brand_describe == '轮胎品牌区'){
                goodsLi += '<li class="first_li tyre" id="1">\
                                <div class="adop-box">\
                                    <p class="subtitle" id="'+val.id+'">轮胎品牌区</p>\
                                </div>\
                                <ul class="bgw">';
            }else{
                goodsLi += '<li class="first_li maintain">\
                                <p class="subtitle" id="'+val.id+'">'+val.brand_describe+'</p>\
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
            // if(myHash != 'null'){
            // if(myHash == val.id){
            // brandLi += '<li class="active"><a href="#'+val.id+'">'+val.name+'</a></li>';
            // }else{
            // brandLi += '<li><a href="#'+val.id+'">'+val.name+'</a></li>';
            // }
            // }else{
            brandLi += '<li><a href="#'+val.id+'">'+val.name+'</a></li>';
            // }
            goodsLi += '<li class="first_li maintain"">\
                            <p class="subtitle" id="'+val.id+'">'+val.name+'</p>\
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
