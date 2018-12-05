
var app = new Vue({
    el: '#app',
    data: {
        images: {
            // 身份证正面
            emblem: 'static/index/image/id2.png',
            // 反面
            portrait: 'static/index/image/id1.png',
            // 营业执照
            businessLicense: 'static/index/image/id3.png',
            // 店面
            storeFront: {
                faceImg: 'static/index/image/id4.png',
                innerImgs: [],
                hasInnerImg: false,
            }
        },
        imageAccept: {
            accept: 'image/jpeg, image/jpg, image/png'
        },
        idCardPopFlag: false,
        licenseFlag: false,
        storeFrontFlag: false,
    },
    methods: {
        emblem(e){
            var reader = new FileReader();
            let $target = e.target || e.srcElement;
            let img = $target.files[0];
            reader.onload = (data) => {
                let res = data.target || data.srcElement;
                this.images.emblem = res.result;
            }
            reader.readAsDataURL(img);
        },
        portrait(e){
            var reader = new FileReader();
            let $target = e.target || e.srcElement;
            let img = $target.files[0];
            reader.onload = (data) => {
                let res = data.target || data.srcElement;
                this.images.portrait = res.result;
            }
            reader.readAsDataURL(img);
        },
        businessLicenseFn(e){
            var reader = new FileReader();
            let $target = e.target || e.srcElement;
            let img = $target.files[0];
            reader.onload = (data) => {
                let res = data.target || data.srcElement;
                this.images.businessLicense = res.result;
            }
            reader.readAsDataURL(img);
        },
        storeFrontFace(e){
            var reader = new FileReader();
            let $target = e.target || e.srcElement;
            let img = $target.files[0];
            reader.onload = (data) => {
                let res = data.target || data.srcElement;
                this.images.storeFront.faceImg = res.result;
            }
            reader.readAsDataURL(img);
        },
        storeFrontInner(e){
            var reader = new FileReader();
            let $target = e.target || e.srcElement;
            let img = $target.files[0];
            if(this.images.storeFront.innerImgs.length < 20){
                reader.onload = (data) => {
                    let res = data.target || data.srcElement;
                    this.images.storeFront.innerImgs.push(res.result);
                    this.images.storeFront.hasInnerImg = true;
                }
                reader.readAsDataURL(img);
            }else{
                layer.open({
                    style: 'bottom:100px;',
                    type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                    skin: 'msg',
                    content: '上传图片不超过20张',
                    time: 1
                })
            }
        },
        idCardPop(){
            // console.log(this.images.emblem)
            // console.log(this.images.portrait)
            return this.idCardPopFlag = !this.idCardPopFlag;
        },
        businessLicensePop(){
            return this.licenseFlag = !this.licenseFlag;
        },
        storeFrontPop(){
            return this.storeFrontFlag = !this.storeFrontFlag;
        },
    }
})
