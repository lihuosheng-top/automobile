var finalMoney;
var deductionId = '';
// 解决计算精度问题
function toFixed(num, s) {
    var times = Math.pow(10, s)
    var des = num * times + 0.5
    des = parseInt(des, 10) / times
    return des + ''
}

// 获取url地址id
var url = location.search;
var id, preId;
if (url.indexOf('?') != -1) {
    id = url.substr(1).split('&')[0].split('=')[1];
    preId = url.substr(1).split('&')[1].split('=')[1];
}

// 地址返回
$.ajax({
    url: 'member_default_address_return',
    type: 'POST',
    dataType: 'JSON',
    success: function (res) {
        console.log(res);
        if (res.status == 1) {
            $('.user-name').text('收货人：' + res.data.harvester);
            $('.user-phone').text(res.data.harvester_phone_num);
            var addr = res.data.address_name.split(',').join('');
            $('.address-txt').text(addr + res.data.harvester_real_address);
            $('.user-info-box').click(function () {
                location.href = 'member_address?id=' + id + '&preid=' + preId;
            })
        }
    },
    error: function () {
        console.log('error');
    }
})
// 商品详情 传过来的数据
$.ajax({
    url: 'return_order_buy_information',
    type: 'POST',
    dataType: 'JSON',
    success: function (res) {
        console.log(res);
        if(res.status === 1){
            // 数量
            $('.quantity-p span').text(res.data.goods_number)
            $('.sundry-cal-val').val(res.data.goods_number)
            // 用户选择的规格
            $('.standard').text(res.data.goods_standard);
            $.each(res.data.goods, function (idx, val) {
                // 店铺名
                $('.order-shop-namp').text(val.store_name);
                // 商品名
                $('.order-goods-p').text(val.goods_name);
                // 商品描述
                $('.order-selling-point').text(val.goods_describe);
                // 价格
                $('.unit-price-p span').text(val.goods_standard_id.price);
                // 总价
                $('.total-money').text(toFixed(res.data.goods_number * val.goods_standard_id.price, 2));
                // 图片
                $('.order-goods-img img').attr('src', 'uploads/' + val.goods_standard_id.images);
            })
            // 总数量
            $('.total-num').text(res.data.goods_number);

            finalMoney = parseFloat($('.total-money').text());

            // 购买数量按钮
            $(function () {
                // 减
                var calculator_val = document.getElementsByClassName('sundry-cal-val')[0];
                $('.sundry-minus').click(function () {
                    if (calculator_val.value > 1) {
                        calculator_val.value -= 1;
                        $('.quantity-p span').text(calculator_val.value);
                        $('.total-num').text(calculator_val.value);
                        // 数量*单价
                        var minusP = toFixed(calculator_val.value * res.data.goods[0].goods_standard_id.price, 2)
                        console.log(minusP)
                        $('.discount').text('不使用积分');
                        $('.default').click();
                        // 用户选择抵扣金额后  增加购买数量
                        if ($('.discount').text() != '不使用积分') {
                            // 保存用户选择的抵扣金额
                            var choseD = $('.discount').text().split('￥')[1];
                            $('.total-money').text(toFixed(minusP - choseD, 2));
                        } else {
                            $('.total-money').text(minusP);
                        }
                        // 保存没有抵扣的金额
                        finalMoney = parseFloat(minusP);
                    } else {
                        calculator_val.value = 1;
                    }
                })
                // 加
                $('.sundry-increase').click(function () {
                    var add = calculator_val.value - 0;
                    add += 1;
                    calculator_val.value = add;
                    $('.quantity-p span').text(calculator_val.value);
                    $('.total-num').text(calculator_val.value);
                    // 数量*单价
                    var increaseP = toFixed(calculator_val.value * res.data.goods[0].goods_standard_id.price, 2)
                    // 用户选择抵扣金额后  增加购买数量
                    if ($('.discount').text() != '不使用积分') {
                        // 保存用户选择的抵扣金额
                        var choseD = $('.discount').text().split('￥')[1];
                        console.log(choseD)
                        $('.total-money').text(toFixed(increaseP - choseD, 2));
                    } else {
                        $('.total-money').text(increaseP);
                    }
                    // 保存没有抵扣的金额
                    finalMoney = parseFloat(increaseP);
                })
            })

            // 返回商品详情
            $('.place-order-back').click(function () {
                location.href = 'goods_detail?id=' + id + '&preid=' + preId;
            })

            // 支付弹窗
            var goodsId = res.data.goods[0].id;
            var storeId = res.data.goods[0].store_id;
            $('#order-buy').click(function () {
                $('.mask').show();
                $('.alipay-pop').animate({ 'bottom': '0' });
                $('html').css('overflow', 'hidden');

                var orderAmount = $('.total-money').text();
                var quantity = $('.total-num').text();
                var goods_standard = $('.standard').text();
                var buy_message = $('.leave-msg').val();
                $.ajax({
                    url: 'ios_api_order_parts_button',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        'goods_id': goodsId,
                        'store_id': storeId,
                        'order_amount': orderAmount,
                        'order_quantity': quantity,
                        'goods_standard': goods_standard,
                        'buy_message': buy_message,
                        'setting_id': deductionId
                    },
                    success: function (res) {
                        console.log(res);
                        $('#WIDout_trade_no').val(res.data.parts_order_number);
                        $('#WIDtotal_amount').val($('.total-money').text());
                        $('#WIDsubject').val(res.data.parts_goods_name);
                        $('#WIDbody').val(res.data.parts_goods_name);

                        $('.close-alipay').click(function () {
                            $('.mask').hide();
                            $('.alipay-pop').animate({ 'bottom': '-100%' });
                            $('html').css('overflow', 'auto');
                            $.ajax({
                                url: 'order_parts_save_record',
                                type: 'POST',
                                dataType: 'JSON',
                                data: {
                                    'store_id': storeId,
                                    'parts_order_number': res.data.parts_order_number
                                },
                                success: function(res){
                                    console.log(res);
                                    location.href = 'order_parts_detail';
                                },
                                error: function(){
                                    console.log('error');
                                }
                            })
                        })
                    },
                    error: function () {
                        console.log('error');
                    }
                })
            })
        }else if(res.status === 3){
            $('.sundry-ul li').first().hide();
            $('.comeIndex').hide();
            var str = '';
            $.each(res.data, function(idx, val){
                str += `<div class="order-goods-info" id="`+val.store_id+`">
                            <div class="order-shop-box">
                                <i class="spr icon-shop"></i>
                                <span class="order-shop-namp">`+val.store_name+`</span>
                            </div>`
                $.each(val.info, function(idx, val){
                    str += `<div class="order-goods-detail">
                                <div class="order-goods-img">
                                    <img src="uploads/`+val.goods_images+`">
                                </div>
                                <div class="order-info-box">
                                    <p class="order-goods-p txt-hid-two">`+val.goods_name+`</p>
                                    <p class="standard txt-hid-two">`+val.special_name+val.goods_delivery+`</p>
                                    <div class="unit-price-quantity">
                                        <p class="unit-price-p">￥<span>`+val.money+`</span></p>
                                        <p class="quantity-p">×<span>`+val.goods_unit+`</span></p>
                                    </div>
                                </div>
                            </div>`
                })
                str += `</div>`;
            })
            $('.user-info-box').after(str);
            // 折扣
            $('.total-money').text(res.data[0].total_price);
            finalMoney = parseFloat($('.total-money').text());
            // 返回商品详情
            $('.place-order-back').click(function () {
                location.href = 'cart_index';
            })
        }
    },
    error: function () {
        console.log('error');
    }
})

// 积分抵扣
$.ajax({
    url: 'return_integral_information',
    type: 'POST',
    dataType: 'JSON',
    success: function (res) {
        console.log(res);
        var str = '';
        $.each(res.data, function (idx, val) {
            str += `<div class="dis">
                        <div class="denomination">
                            <p class="deduction-money">￥`+ val.deductible_money + `</p>
                            <span class="need-integ">需`+ val.integral_full + `积分</span>
                        </div>
                        <label class="twent" for="twent-`+ val.setting_id + `" id="` + val.consumption_full + `">
                            <p class="dis-introduce">消费满`+ val.consumption_full + `元可用</p>
                        </label>
                        <input type="radio" id="twent-`+ val.setting_id + `" name="disc">
                    </div>`
        })
        $('.discount_wrap').append(str);

        $('.dis').click(function (e) {
            e.preventDefault();
            var index = $(this).index() - 1;
            var consumptionFul = $(this).find('label')[0].id;
            if (finalMoney >= consumptionFul) {
                var cut = $(this).find('.deduction-money').text().split('￥')[1];
                $(this).find('input').attr('checked', 'checked');
                $('.discount').text('-￥' + cut);
                $('.discount_pop').hide();
                $('.place-order-pop').show();
                $(this).siblings().find('input').removeAttr('checked');
                $(this).find('input').prop('checked', 'checked');

                // 抵扣后的总价格
                $('.total-money').text(toFixed(finalMoney - cut, 2));
                // 存储积分券id
                deductionId = $(this).find('input')[0].id.split('-')[1];
                console.log(deductionId)
            } else {
                layer.open({
                    skin: 'msg',
                    content: '未达到使用条件',
                    time: 1
                })
            }
        })
        $('.default').click(function (e) {
            e.preventDefault();
            // 隐藏弹窗
            $('.discount_pop').hide();
            $('.place-order-pop').show();
            // 显示 不适用积分
            $(this).siblings().find('input').removeAttr('checked');
            var dont = $(this).find('label').text();
            $(this).find('input').prop('checked', 'checked');
            $('.discount').text(dont);
            // 不适用积分 重新计算价格
            $('.total-money').text(toFixed(finalMoney, 2));
        })

    },
    error: function () {
        console.log('error');
    }
})
$('.icon_back').click(function () {
    $('.discount_pop').hide();
    $('.place-order-pop').show();
})
$('.deduction').click(function () {
    $('.discount_pop').show();
    $('.place-order-pop').hide();
})

// 选择支付方式
$('.widway').click(function(){
    $('.alipay-pop').animate({'bottom': '-100%'});
    $('.chose-payment').animate({'bottom': '0'});
})
function backMethod(){
    $('.alipay-pop').animate({'bottom': '0'});
    $('.chose-payment').animate({'bottom': '-100%'});
}
$('.method-div').click(function(){
    $(this).siblings().find('.check-a').removeClass('icon-check');
    $(this).find('.check-a').addClass('icon-check');
    var paymentMethod = $(this).find('p').text();
    $('.widway').val(paymentMethod);
    backMethod();
    
})
$('.chose-payment-back').click(function(){
    backMethod();
})