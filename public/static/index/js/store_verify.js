
const formData = new FormData();
var app = new Vue({
    el: '#app',
    data: {
        images: {
            // 身份证正面
            emblem: 'static/index/image/id2.png',
            emblemFile: '',
            // 反面
            portrait: 'static/index/image/id1.png',
            portraitFile: '',
            // 营业执照
            businessLicense: 'static/index/image/id3.png',
            businessLicenseFile: '',
            // 许可证
            license: 'static/index/image/id4.png',
            licenseFile: '',
            // 店面
            storeFront: {
                faceImg: 'static/index/image/id5.png',
                faceImgFile: '',
                innerImgs: [],
                innerImgsFile: [],
                hasInnerImg: false,
            }
        },
        imageAccept: {
            accept: 'image/jpeg, image/jpg, image/png'
        },
        idCardPopFlag: false,
        licenseFlag: false,
        storeFrontFlag: false,
        backInfo: '',
    },
    created(){
        this.getData();
    },
    methods: {
        // 上传身份证国徽面
        emblem(e){
            var img = e.target.files[0];
            this.images.emblemFile = img;
            console.log(this.images.emblemFile);
            var reader = new FileReader();
            var $this = this;
            reader.readAsDataURL(img);
            reader.onload = function(e){
                $this.images.emblem = this.result;
            }
        },
        // 上传身份证人像面
        portrait(e){
            var img = e.target.files[0];
            this.images.portraitFile = img;
            // console.log(this.images.portraitFile);
            var reader = new FileReader();
            var $this = this;
            reader.readAsDataURL(img);
            reader.onload = function(){
                $this.images.portrait = this.result;
            }
        },
        // 上传营业执照
        businessLicenseFn(e){
            var img = e.target.files[0];
            this.images.businessLicenseFile = img;
            // console.log(this.images.businessLicenseFile);
            var reader = new FileReader();
            var $this = this;
            reader.readAsDataURL(img);
            reader.onload = function(){
                $this.images.businessLicense = this.result;
            }
        },
        // 上传许可证
        licenseFn(e){
            var img = e.target.files[0];
            this.images.licenseFile = img;
            // console.log(this.images.licenseFile);
            var reader = new FileReader();
            var $this = this;
            reader.readAsDataURL(img);
            reader.onload = function(){
                $this.images.license = this.result;
            }
        },
        // 上传门脸图片
        storeFrontFace(e){
            var img = e.target.files[0];
            this.images.storeFront.faceImgFile = img;
            // console.log(this.images.storeFront.faceImgFile);
            var reader = new FileReader();
            var $this = this;
            reader.readAsDataURL(img);
            reader.onload = function(){
                $this.images.storeFront.faceImg = this.result;
            }
        },
        // 上传店内图片
        storeFrontInner(e){
            // console.log(e.target.files)
            var imgArr = e.target.files;
            // console.log(imgArr)
            var innerImgsFile = this.images.storeFront.innerImgsFile;
            for(var i = 0, len = imgArr.length; i < len; i++){
                if(innerImgsFile.length < 20){
                    innerImgsFile.push(imgArr[i]);
                    formData.append('verifying_physical_storefront_two[]', imgArr[i]);
                    this.imagesAdd(imgArr[i]);
                }else{
                    layer.open({
                        style: 'bottom:100px;',
                        type: 0,//弹窗类型 0表示信息框，1表示页面层，2表示加载层
                        skin: 'msg',
                        content: '上传图片不超过20张',
                        time: 1
                    })
                }
            }
        },
        // 多图
        imagesAdd(images){
            var reader = new FileReader();
            var innerImgs = this.images.storeFront.innerImgs;
            var $this = this;
            reader.readAsDataURL(images);
            reader.onload = function () {
                images.src = this.result;
                innerImgs.push(images);
                $this.images.storeFront.hasInnerImg = true;
            }
        },
        // 显示身份证弹窗
        idCardPop(){
            return this.idCardPopFlag = !this.idCardPopFlag;
        },
        // 显示营业执照弹窗
        businessLicensePop(){
            return this.licenseFlag = !this.licenseFlag;
        },
        // 显示实体店面弹窗
        storeFrontPop(){
            return this.storeFrontFlag = !this.storeFrontFlag;
        },
        // 提交申请
        submitData(){
            var $images = this.images;
            if($images.emblemFile !== '' && $images.portraitFile !== '' && $images.businessLicenseFile !== ''
                && $images.licenseFile !== '' && $images.storeFront.innerImgsFile.length !== 0){
                formData.append('store_identity_card', $images.emblemFile);
                formData.append('store_reverse_images', $images.portraitFile);
                formData.append('store_do_bussiness_positive_img', $images.businessLicenseFile);
                formData.append('store_do_bussiness_side_img', $images.licenseFile);
                formData.append('verifying_physical_storefront_one', $images.storeFront.faceImgFile);
                console.log($images.emblemFile);
                console.log($images.storeFront.innerImgsFile);
                this.$http.post('store_update', formData)
                .then(res => {
                    this.backInfo = eval('('+ res.data +')').data;
                    console.log(eval('('+ res.data +')'));
                }).catch(err => {
                    console.log(err);
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
        },
        // 获取数据
        getData(){
            this.$http.post('return_store_information')
            .then(res => {
                if(eval('('+ res.data +')').data.status !== 0){
                    this.backInfo = eval('('+ res.data +')').data;
                }
                console.log(eval('('+ res.data +')').data);
            }).catch( err => {
                console.log(err);
            })
        },
    }
})
