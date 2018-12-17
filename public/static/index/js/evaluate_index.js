
// 初始化star
function initStar(){
    layui.use(['rate'], function(){
        var rate = layui.rate;
        layui.each($('.star'), function(idx, elem){
            rate.render({
                elem: elem,
                value: 3,
                text: true,
                setText: function(val){
                    var arrs = {
                        '1': '很差',
                        '2': '差',
                        '3': '一般',
                        '4': '好',
                        '5': '很好',
                    };
                    this.span.text(arrs[val]);
                }
            })
        })
    })
}

