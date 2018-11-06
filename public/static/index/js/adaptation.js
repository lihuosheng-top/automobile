var screenW = document.documentElement.clientWidth || document.body.clientWidth;
var htmlDom = document.getElementsByTagName('html')[0];
htmlDom.style.fontSize = screenW / 7.5 + 'px';
window.addEventListener('resize', function(){
    var screenW = document.documentElement.clientWidth || document.body.clientWidth;
    htmlDom.style.fontSize = screenW / 7.5 + 'px';
})