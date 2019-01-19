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
            $('.user-name').text('收货人：' + res.data.harvester).data({'address_id': res.data.id});
            $('.user-phone').text(res.data.harvester_phone_num);
            var addr = res.data.address_name.split(',').join('');
            $('.address-txt').text(addr + res.data.harvester_real_address);
            $('.user-info-box').click(function () {
                location.href = 'member_address?id=' + id + '&preid=' + preId;
            })
            $('.invoice-phone span').text(res.data.user_phone_number);
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
                        <label class="twent" for="twent-`+ val.setting_id + `">
                            <p class="dis-introduce">消费满<span>`+ val.consumption_full + `</span>元可用</p>
                        </label>
                        <input type="radio" id="twent-`+ val.setting_id + `" name="disc">
                    </div>`
        })
        $('.discount_wrap').append(str);

        $('.dis').click(function (e) {
            e.preventDefault();
            // var index = $(this).index() - 1;
            var consumptionFul = $(this).find('.dis-introduce span').text();
            if (+totalMoney >= consumptionFul) {
                var cut = $(this).find('.deduction-money').text().split('￥')[1];
                $(this).find('input').prop('checked', 'checked');
                $('.discount').text('-￥' + cut);
                $('.discount_pop').hide();
                $('.place-order-pop').show();

                // 抵扣后的总价格
                $('.total-money').text(toFixed(totalMoney - cut, 2));
                // 存储积分券id
                deductionId = $(this).find('input')[0].id.split('-')[1];
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
            $('.total-money').text(toFixed(totalMoney, 2));
        })

    },
    error: function () {
        console.log('error');
    }
})
$('.discount_pop .icon_back').click(function () {
    $('.discount_pop').hide();
    $('.place-order-pop').show();
})
$('.deduction').click(function () {
    $('.discount_pop').show();
    $('.place-order-pop').hide();
})
// 发票
$('.invoice-li').click(function(){
    $('.invoice-container').show();
    $('.place-order-pop').hide();
})
$('.invoice-type-box').on('click', 'span', function(){
    $(this).addClass('choose').siblings().removeClass('choose');
    if($(this).hasClass('need')){
        $('.need-invoice').show();
    }else{
        $('.need-invoice').hide();
    }
})
$('.invoice-object').on('change', 'input', function(){
    if(this.id === 'personal'){
        $('.personal-invoice-detail').show();
        $('.company-invoice-detail').hide();
    }else{
        $('.personal-invoice-detail').hide();
        $('.company-invoice-detail').show();
    }
})
$('.invoice-container .icon_back').click(function(){
    $('.invoice-container').hide();
    $('.place-order-pop').show();
})

$('.invoice-btn').click(function(){
    var text = $('.choose').text();
    if(text == '不开发票'){
        hideInvoicePop();
        $('.invoice').text(text);
        // 不开发票 隐藏税费 手续费
        $('.poundage-li').hide();
        $('.taxation-li').hide();
        // 传0 总价钱减去 税费 手续费
        invoiceAjax(totalMoney, 0);
    }else{
        var invoiceObjId = $('.invoice-object input:checked').attr('id');
        if(invoiceObjId == 'personal'){
            var personalHead = $('.invoice-header span').text();
            var personalPhone = $('.invoice-phone span').text();
            invoiceAjax(totalMoney, 1);
            $('.invoice').text('个人发票');
        }else{
            if($('#company-header-input').val() !== '' && $('#company-phone-input').val() !== ''){
                var companyHead = $('#company-header-input').val();
                var companyPhone = $('#company-phone-input').val();
                var companyIdentify = $('#company-identify').val();
                invoiceAjax(totalMoney, 1);
                $('.invoice').text('公司发票');
            }else{
                layer.open({
                    skin: 'msg',
                    content: '请填写发票信息',
                    time: 1
                })
            }
        }
    }
})
function invoiceAjax(money, key){
    hideInvoicePop();
    $.ajax({
        url: 'invoice_index',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'invoice_money': money
        },
        success: function(res){
            console.log(res);
            if(res.status == 1){
                if(key === 1){
                    $('.poundage-li').show().find('.poundage span').text(res.data.poundage);
                    $('.taxation-li').show().find('.taxation span').text(res.data.taxation);
                    $('.total-money').text(toFixed(parseFloat(money)+res.data.poundage+res.data.taxation, 2));
                }else{
                    $('.poundage-li').find('.poundage span').text('');
                    $('.taxation-li').find('.taxation span').text('');
                    $('.total-money').text(toFixed(parseFloat(money)-(res.data.poundage+res.data.taxation), 2));
                }
            }
        },
        error: function(res){
            console.log(res.status, res.statusText);
        }
    })
}
function hideInvoicePop(){
    $('.invoice-container').hide();
    $('.place-order-pop').show();
}
