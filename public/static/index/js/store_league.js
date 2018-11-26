
var app = new Vue({
    el: '#app',
    data: {
        images: [],
        imageAccept: {
            accept: 'image/jpeg, image/jpg, image/png'
        }
    },
    methods: {
        uploadImg: function(e){
            console.log(e.target)
            // var reader = new FileReader();
            // var img = e.target.files[0];
            // var imgType=img.type;//文件的类型，判断是否是图片
            // var imgSize=img.size;//文件的大小，判断图片的大小
            // if(this.imageAccept.accept.indexOf(imgType) == -1){
            //     layer.open({
            //         style: 'bottom:100px;',
            //         type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
            //         skin: 'msg',
            //         content: '图片上传格式不正确',
            //         time: 1
            //     })
            // }
            // if(imgSize > 3145728){
            //     layer.open({
            //         style: 'bottom:100px;',
            //         type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
            //         skin: 'msg',
            //         content: '上传图片需小于3M',
            //         time: 1
            //     })
            // }
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
            //         _this.images.push(uri);
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
        }
    }
})