
var app = new Vue({
    el: '#app',
    data: {
        imageUrl: 'static/index/image/icon8.png',
        imageAccept: {
            accept: 'image/jpeg, image/jpg, image/png'
        },
        businessPopFlag: false,
    },
    methods: {
        uploadImg(e){
            var reader = new FileReader();
            let $target = e.target || e.srcElement;
            let img = $target.files[0];

            var imgType=img.type;//文件的类型，判断是否是图片
            var imgSize=img.size;//文件的大小，判断图片的大小
            if(this.imageAccept.accept.indexOf(imgType) == -1){
                layer.open({
                    style: 'bottom:100px;',
                    type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                    skin: 'msg',
                    content: '图片上传格式不正确',
                    time: 1
                })
            }else if(imgSize > 3145728){
                layer.open({
                    style: 'bottom:100px;',
                    type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                    skin: 'msg',
                    content: '上传图片需小于3M',
                    time: 1.5
                })
            }else{
                reader.onload = (data) => {
                    let res = data.target || data.srcElement;
                    this.imageUrl = res.result;
                }
                reader.readAsDataURL(img);
            }
            
            // var uri = '';
            // var formData = new FormData(); 
            // formData.append('file',img,img.name);
            // this.$http.post('/file/upload',formData,{
            //     headers:{'Content-Type':'multipart/form-data'}
            // }).then(response => {
            //     console.log(response.data)
            //     uri = response.data.url
            //     reader.readAsDataURL(img);
            //     var _this=this;
            //     reader.onloadend=function(){
            //         _this.imageUrl = uri;
            //     }
            // }).catch(error => {
            //     layer.open({
            //         style: 'bottom:100px;',
            //         type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
            //         skin: 'msg',
            //         content: '图片上传出错',
            //         time: 1
            //     })
            // })   
        },
        showBusinessPop(){
            return this.businessPopFlag = true;
        },
        hideBusinessPop(){
            return this.businessPopFlag = false;
        },
        nextButton(){
            location.href = 'store_verify';
        }
    }
})


// http://www.jq22.com/jquery-info8371 插件地址  三级城市
var area = new LArea();
area.init({
    'trigger': '#area-li',//触发选择控件的文本框，同时选择完毕后name属性输出到该位置
    'valueTo': '#value1',//选择完毕后id属性输出到该位置
    'keys': { //绑定数据源相关字段 id对应valueTo的value属性输出 name对应trigger的value属性输出
        id: 'id',
        name: 'name'
    },
    'type': 1,//数据源类型
    'data': LAreaData//数据源
})
area.value=[1,13,1];//控制初始位置，注意：该方法并不会影响到input的value