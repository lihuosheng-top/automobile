/**
 * Created by Hanger on 2017/8/31.
 */
(function(global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
        typeof define === 'function' && define.amd ? define(factory) :
        (global.CityPicker = factory())
}(this, (function() {
    /**
     * 以 id 获取 DOM
     */
    function $id(id) {
        return document.getElementById(id)
    }
    
    /**
     * 设置子元素样式
     */
    function setChildStyle(parent, key, val) {
        var children = parent.children
        for (var i = 0; i < children.length; i++) {
            children[i].style[key] = val
        }
    }

    /**
     * DOM 移除自身
     */
    function $removeSelf(dom) {
        dom.parentNode.removeChild(dom)
    }

    /**
     * 创建选择器构造函数
     */
    function CityPicker(config) {
        this.inputId = config.inputId // 目标DOM元素ID，必填
        this.data = config.data // json 数据，必填
        this.initialOption = config.initialOption || null // 规定初始显示的选项，选填
        this.valueKey = config.valueKey || 'value' // 需要展示的数据的键名，选填
        this.childKey = config.childKey || 'child' // 子数据的键名，选填
        this.success = config.success // 确定按钮回调函数，必填
        this.cancel = config.cancel || null // 取消按钮回调函数，选填
        this.beforeShow = config.beforeShow || null// 规定呼起选择器前的逻辑，选填
        this.title = config.title || '' // 选择器标题，选填
        this.sureText = config.sureText || '确定' // 确定按钮文本，选填
        this.cancelText = config.cancelText || '取消' // 取消按钮文本，选填
        this.a = config.a || 0.001 // 惯性滚动加速度（正数, 单位 px/(ms * ms)），选填，默认 0.001
        this.style = config.style // 选择器样式, 选填
        this.initTab() // 初始化标签
        this.initUI() // 初始化UI
        this.initEvent() // 初始化事件
    }

    /**
     * 定义构造函数的原型
     */
    CityPicker.prototype = {
        // 明确构造器指向
        constructor: CityPicker,
        /**
         * 定义初始化标签函数
         */
        initTab: function() {
            this.wrapId = this.inputId + '-wrap' // 选择器外包裹元素ID
            this.relatedArr = [] // 存放每列地址的关联数组
            this.cityIndex = [] // 存放每列地址的索引
            this.liNum = [] // 每个ul有多少个可选li
            this.ulCount = 0 // 当前展示的列数
            this.renderCount = 0 // 将要渲染的列数
            this.liHeight = this.style && this.style.liHeight ? this.style.liHeight : 40 // 每个li的高度
            this.btnHeight = this.style && this.style.btnHeight ? this.style.btnHeight : 44 // 按钮的高度
            this.cityUl = [] // 每个ul元素
            this.curDis = [] // 每个ul当前偏离的距离
            this.curPos = [] // 记录 touchstart 时每个ul的竖向距离
            this.startY = 0 // touchstart的位置
            this.startTime = 0 // touchstart的时间
            this.endTime = 0 // touchend的时间
            this.moveY = 0 // touchmove的位置
            this.moveTime = 0 // touchmove的时间
            this.moveNumber = 1 // touchmove规定时间间隔下的次数
            this.moveSpeed = [] // touchmove规定时间间隔下的平均速度
            this.abled = true // 标识滚动是否进行中
            this.container = this.wrapId + '-container' // 选择器容器ID
            this.box = this.wrapId + '-box' // 选择器按钮区域ID
            this.content = this.wrapId + '-content' // 选择器选择区域ID
            this.abolish = this.wrapId + '-abolish' // 选择器取消按钮ID
            this.sure = this.wrapId + '-sure' // 选择器确定按钮ID
            this.isSelect = true // 是否呼起选择框
        },
        /**
         * 定义初始化 UI 函数
         */
        initUI: function() {
            // 创建选择器的外包裹元素
            this.createContainer()
            // 初始化最高层的参数，最高层的关联数组在未来的操作中都无需更新
            this.relatedArr[0] = this.data
            this.liNum[0] = this.relatedArr[0].length
            if(this.initialOption) {
                this.setInitailOption(this.initialOption, true)
            } else {
                this.cityIndex[0] = 0
                this.curDis[0] = 0
                // 得到各列的关联数组
                this.getRelatedArr(this.relatedArr[0][0], 0)
                // 初始化子数据参数，子数据的关联数组会随着选中父数据的改变而变化
                this.updateChildData(0)
                // 初始化选择器内容
                this.renderContent()
            }
        },
        /**
         * 定义初始化事件函数
         */
        initEvent: function() {
            var that = this
            var wrap = that.wrap
            var container = $id(that.container)
            // 点击目标DOM元素显示选择器
            $id(that.inputId).addEventListener('click', function() {
                that.beforeShow && that.beforeShow()
                if(that.isSelect) that.show(wrap, container)
            })
            // 点击确定按钮隐藏选择器并输出结果
            $id(that.sure).addEventListener('click', function() {
                that.success(that.getResult())
                that.hide(wrap, container)
            })
            // 点击取消隐藏选择器
            $id(that.abolish).addEventListener('click', function() {
                that.cancel && that.cancel()
                that.hide(wrap, container)
            })
            // 点击背景隐藏选择器
            wrap.addEventListener('click', function(e) {
                if (e.target.id === that.wrapId && wrap.classList.contains("hg-picker-bg-show")) {
                    that.cancel && that.cancel()
                    that.hide(wrap, container)
                }
            })
        },
        /**
         * 计算并返回当前项所在的位置
         * Explain : @arr 需要初始显示的项
         *  @isInit 是否是初始化状态
         */
        setInitailOption: function(arr, isInit) {
            var idxArr = []
            for (var i = 0; i < arr.length; i++) {
                if (i === 0) {
                    var idx = this.getValue(this.data).indexOf(arr[i])
                    if(idx > -1) idxArr.unshift(idx)
                    else throw Error('The matching initailOption cannot be found')
                } else {
                    this.getRelatedArr(this.relatedArr[i - 1][idxArr[0]], i - 1)
                    var idx = this.getValue(this.relatedArr[i]).indexOf(arr[i])
                    if(idx > -1) idxArr.unshift(idx)
                    else throw Error('The matching initailOption cannot be found')
                }
            }
            var idxMark = idxArr.reverse()
            this.ulCount = idxMark.length
            this.cityIndex = idxMark
            for (var i = 0; i < idxMark.length; i++) {
                this.curDis[i] = -1 * this.liHeight * idxMark[i]
                if (i >= 1) this.liNum[i] = this.relatedArr[i].length
            }
            if (isInit) {
                // 初始化选择器内容
                this.renderContent()
                for (var i = 0; i < this.ulCount; i++) this.roll(i)
            } else {
                for (var i = 0; i < this.ulCount; i++) {
                    this.updateView(i)
                    this.roll(i)
                }
            }
        },
        /**
         * 创建选择器外包裹元素
         */
        createContainer: function() {
            var div = document.createElement("div")
            div.id = this.wrapId
            document.body.appendChild(div)
            this.wrap = $id(this.wrapId)
            this.wrap.classList.add('hg-picker-bg')
        },
        /**
        * 获取当前列后的关联数组
        * Explain : @obj 当前选中数据的子数据
            @i 当前操作列索引
        */
        getRelatedArr: function(obj, i) {
            if (typeof obj === 'object') {
                if (this.childKey in obj && obj[this.childKey].length > 0) {
                    this.relatedArr[i + 1] = obj[this.childKey]
                    this.renderCount++
                    this.getRelatedArr(obj[this.childKey][0], ++i)
                }
            }
        },
        /**
        * 更新 ulCount 和子数据的参数
        * Explain : @i 当前操作列索引
            当前操作列的关联数组不需要更新，只需更新其子数据中的关联数组
            ulCount, liNum, cityIndex, curDis
        */
        updateChildData: function(i) {
            this.ulCount = i + 1 + this.renderCount
            for (var j = i + 1; j < this.ulCount; j++) {
                this.liNum[j] = this.relatedArr[j].length
                this.cityIndex[j] = 0
                this.curDis[j] = 0
            };
        },
        /**
         * 获取每列关联数据中需要被展示的数据
         * Return : Array
         * Explain : @arr 需要被取值的对象数组
         */
        getValue: function(arr) {
            var tempArr = []
            for (var i = 0; i < arr.length; i++) {
                if (typeof arr[i][this.valueKey] === 'object') tempArr.push(arr[i][this.valueKey][this.valueKey])
                else tempArr.push(arr[i][this.valueKey])
            }
            return tempArr
        },
        /**
         * 渲染地区选择器的内容
         */
        renderContent: function() {
            var btnHTML = '<div class="hg-picker-btn-box" id="' + this.box + '">' + this.title +
                '<div class="hg-picker-btn" id="' + this.abolish + '">' + this.cancelText + '</div>' +
                '<div class="hg-picker-btn" id="' + this.sure + '">' + this.sureText + '</div>' +
                '</div>'

            var contentHtml = '<div class="hg-picker-content" id="' + this.content + '">' +
                '<div class="hg-picker-up-shadow"></div>' +
                '<div class="hg-picker-down-shadow"></div>' +
                '<div class="hg-picker-line"></div>' +
                '</div>'

            // 设置按钮位置
            if (this.style && this.style.btnLocation === 'bottom') {
                var html = '<div  class="hg-picker-container" id="' + this.container + '">' +
                    contentHtml + btnHTML +
                    '</div>'
            } else {
                var html = '<div  class="hg-picker-container" id="' + this.container + '">' +
                    btnHTML + contentHtml +
                    '</div>'
            }

            this.wrap.innerHTML = html
            for (var i = 0; i < this.ulCount; i++) {
                this.renderUl(i)
                this.bindRoll(i)
            }
            this.setStyle()
            this.setUlWidth()
        },
        /**
         * 设置选择器样式
         */
        setStyle: function() {
          if (!this.style) return
          var obj = this.style
          var container = $id(this.container)
          var content = $id(this.content)
          var box = $id(this.box)
          var sureBtn = $id(this.sure)
          var cancelBtn = $id(this.abolish)
          var len = content.children.length
          // 设置高度
          if (obj.liHeight !== 40) {
            for (var i = 0; i < this.ulCount; i++) {
              setChildStyle(content.children[i], 'height', this.liHeight + 'px')
            };
            content.children[len - 3].style.height = this.liHeight * 2 + 'px'
            content.children[len - 2].style.height = this.liHeight * 2 + 'px'
            content.children[len - 1].style.height = this.liHeight + 'px'
            content.children[len - 1].style.top = this.liHeight * 2 + 'px'
            content.style.height = this.liHeight * 5 + 'px'
            content.style.lineHeight = this.liHeight + 'px'
          }
          if (obj.btnHeight !== 44) {
            box.style.height = this.btnHeight + 'px'
            box.style.lineHeight = this.btnHeight + 'px'
          }
          if (obj.btnOffset) {
            sureBtn.style.marginRight = obj.btnOffset
            cancelBtn.style.marginLeft = obj.btnOffset
          }
          if (obj.liHeight !== 40 || obj.btnHeight !== 44) container.style.height = this.liHeight * 5 + this.btnHeight + 'px'
          // 设置配色
          if(obj.titleColor) box.style.color = obj.titleColor
          if(obj.sureColor) sureBtn.style.color = obj.sureColor
          if(obj.cancelColor) cancelBtn.style.color = obj.cancelColor
          if(obj.btnBgColor) box.style.backgroundColor = obj.btnBgColor
          if(obj.contentColor) content.style.color = obj.contentColor
          if(obj.contentBgColor) content.style.backgroundColor = obj.contentBgColor
          if(obj.upShadowColor) content.children[len - 3].style.backgroundImage = obj.upShadowColor
          if(obj.downShadowColor) content.children[len - 2].style.backgroundImage = obj.downShadowColor
          if(obj.lineColor) content.children[len - 1].style.borderColor = obj.lineColor
        },
        /**
         * 渲染 ul 元素
         * Explain : @i 需要处理的列的索引
         */
        renderUl: function(i) {
            var parentNode = $id(this.content)
            var newUl = document.createElement('ul')
            newUl.setAttribute('id', this.wrapId + '-ul-' + i)
            parentNode.insertBefore(newUl, parentNode.children[parentNode.children.length - 3])
            this.cityUl[i] = $id(this.wrapId + '-ul-' + i)
            this.renderLi(i)
        },
        /**
         * 渲染 li 元素
         * Explain : @i 需要处理的列的索引
         */
        renderLi: function(i) {
            this.cityUl[i].innerHTML = ''
            var lis = '<li></li><li></li>'
            this.getValue(this.relatedArr[i]).forEach(function(val, index) {
                lis += '<li>' + val + '</li>'
            })
            lis += '<li></li><li></li>'
            this.cityUl[i].innerHTML = lis
            if (this.liHeight !== 40) setChildStyle(this.cityUl[i], 'height', this.liHeight + 'px')
        },
        /**
         * 设置 ul 元素宽度
         */
        setUlWidth: function() {
            for (var i = 0; i < this.ulCount; i++) {
                this.cityUl[i].style.width = (100 / this.ulCount).toFixed(2) + '%'
            }
        },
        /**
         * 绑定滑动事件
         * Explain : @i 需要处理的列的索引
         */
        bindRoll: function(i) {
            var that = this
            that.cityUl[i].addEventListener('touchstart', function() {
                that.touch(i)
            }, false)
            that.cityUl[i].addEventListener('touchmove', function() {
                that.touch(i)
            }, false)
            that.cityUl[i].addEventListener('touchend', function() {
                that.touch(i)
            }, true)
        },
        /**
         * 控制列表的滚动
         * Explain : @i 需要处理的列的索引
         * @time 滚动持续时间
         */
        roll: function(i, time) {
            if (this.curDis[i] || this.curDis[i] === 0) {
                this.cityUl[i].style.transform = 'translate3d(0, ' + this.curDis[i] + 'px, 0)'
                this.cityUl[i].style.webkitTransform = 'translate3d(0, ' + this.curDis[i] + 'px, 0)'
                if (time) {
                    this.cityUl[i].style.transition = 'transform ' + time + 's ease-out'
                    this.cityUl[i].style.webkitTransition = '-webkit-transform ' + time + 's ease-out'
                }
            }
        },
        /**
         * 地区选择器触摸事件
         * Explain : @i 需要处理的列的索引
         */
        touch: function(i) {
            var event = event || window.event
            event.preventDefault()
            switch (event.type) {
                case "touchstart":
                    this.startTime = new Date()
                    // 列表滚动中禁止二次操作
                    if (this.startTime - this.endTime < 200) {
                        this.abled = false
                        return
                    } else this.abled = true
                    this.startY = event.touches[0].clientY
                    this.curPos[i] = this.curDis[i] // 记录当前位置
                    this.moveNumber = 1
                    this.moveSpeed = []
                    break
                case "touchmove":
                    if (!this.abled) return
                    event.preventDefault()
                    this.moveY = event.touches[0].clientY
                    var offset  = this.startY - this.moveY // 向上为正数，向下为负数
                    this.curDis[i] = this.curPos[i] - offset
                    if (this.curDis[i] >= 1.5 * this.liHeight) this.curDis[i] = 1.5 * this.liHeight
                    if (this.curDis[i] <= -1 * (this.liNum[i] - 1 + 1.5) * this.liHeight) this.curDis[i] = -1 * (this.liNum[i] - 1 + 1.5) * this.liHeight
                    this.roll(i)
                    // 每运动 130 毫秒，记录一次速度
                    if (this.moveTime - this.startTime >= 130 * this.moveNumber) {
                        this.moveNumber++
                        this.moveSpeed.push(offset / (this.moveTime - this.startTime))
                    }
                    break
                case "touchend":
                    if (!this.abled) return
                    this.endTime = Date.now()
                    if (this.moveNumber === 1) {
                        var speed =  (this.startY - event.changedTouches[0].clientY) / (this.endTime - this.startTime)
                    } else {
                        var speed = this.moveSpeed[this.moveSpeed.length - 1]
                    }
                    this.curDis[i] = this.curDis[i] - this.calculateBuffer(speed, this.a)
                    this.fixate(i)
                    break
            }
        },
        /**
         * 计算滚动缓冲距离
         * Return : Number
         * Explain : @v 速度（正负表示运动方向, 单位 px/ms）
         * @a 加速度（正数, 单位 px/(ms * ms)）
         */
        calculateBuffer: function (v, a) {
            if (Math.abs(v) < 0.25) return 0
            else return (v / Math.abs(v)) * (0.5 * v * v / a)
        },
        /**
         * 固定 ul 最终的位置、更新视图
         * Explain : @i 需要处理的列的索引
         */
        fixate: function(i) {
            this.renderCount = 0
            this.getPosition(i)
            this.getRelatedArr(this.relatedArr[i][this.cityIndex[i]], i)
            this.updateChildData(i)
            this.updateView(i)
            for (var j = i; j < this.ulCount; j++) this.roll(j, 0.2)
        },
        /**
         * 获取定位数据
         * Explain : @i 需要处理的列的索引
         */
        getPosition: function(i) {
            if (this.curDis[i] <= -1 * (this.liNum[i] - 1) * this.liHeight ) this.cityIndex[i] = this.liNum[i] - 1
            else if (this.curDis[i] >= 0) this.cityIndex[i] = 0
            else this.cityIndex[i] = -1 * Math.round(this.curDis[i] / this.liHeight)
            this.curDis[i] = -1 * this.liHeight * this.cityIndex[i]
        },
        /**
         * 更新内容区视图
         * Explain : @i 需要处理的列的索引
         */
        updateView: function(i) {
            var curUlCount = $id(this.content).children.length - 3
            if (this.ulCount == curUlCount) { // 列数不变的情况
                for (var j = i + 1; j < this.ulCount; j++) {
                    this.renderLi(j)
                }
            } else if (this.ulCount > curUlCount) { // 列数增加的情况
                for (var j = i + 1; j < curUlCount; j++) {
                    this.renderLi(j)
                }
                for (var j = curUlCount; j < this.ulCount; j++) {
                    this.renderUl(j)
                    this.bindRoll(j)
                }
                this.setUlWidth()
            } else { // 列数减少的情况
                for (var j = i + 1; j < this.ulCount; j++) {
                    this.renderLi(j)
                }
                for (var j = this.ulCount; j < curUlCount; j++) {
                    $removeSelf(this.cityUl[j])
                }
                this.setUlWidth()
            }
        },
        /**
         * 获取结果的数组
         */
        getResult: function() {
            var arr = []
            for (var i = 0; i < this.ulCount; i++) {
                arr.push(this.relatedArr[i][this.cityIndex[i]])
            }
            return arr
        },
        /**
         * 显示选择器
         * Explain : @wrap 包裹层 DOM 元素
            @container 内容层 Dom 元素
         */
        show: function(wrap, container) {
            wrap.classList.add('hg-picker-bg-show')
            container.classList.add('hg-picker-container-up')
        },
        /**
         * 隐藏选择器
         * Explain : @wrap 包裹层 DOM 元素
            @container 内容层 Dom 元素
         */
        hide: function(wrap, container) {
            wrap.classList.remove('hg-picker-bg-show')
            container.classList.remove('hg-picker-container-up')
        },
        /**
         * 是否禁止呼起选择框
         */
        forbidSelect: function(status) {
            this.isSelect = !status
        },
    }

    return CityPicker
})))
