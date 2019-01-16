
$(function(){


//	抽屉效果;
$(".UCleft-fixed").children("dl").on("click","dt",function(){
//	alert(111);
	if($(this).parent("dl").hasClass("curr")){
		$(this).parent("dl").removeClass("curr");
	}
	else{
		$(this).parent("dl").addClass("curr");
    	$(this).parent("dl").siblings().removeClass("curr");
	}

  });

  $(".UCleft-fixed").children("dl").children("dd").on("click","p",function(){
  	
  $(this).css('font-weight','bold');
  $(this).siblings().css('font-weight','500');
  $(this).parent("dd").parent("dl").siblings().children("dd").children("p").css('font-weight','500');
   var url=$(this).data("url");
   var dk = $(window.parent.document).find("#add").attr("src"); 
			$.ajax({
				type : "get",
				url: url,
				cache: true,
				success: function(html){
					$('#add').attr('src',url);
				}
				
			});
  });

    
     //  图片上传限制
        function imglimit(a){
        
        	var img=a.parent().parent().parent().parent().parent().siblings(".imgcontent").children("span").length;
        	 var max_list=a.data("id")-img;

        	 a.data('id',max_list);
            console.log(a.data("id"));
        }
       
       setTimeout(
       	function(){ 
       		for(var i=0;i<$(".file").length;i++){
        		imglimit($("#"+$(".file")[i].id));
        } }, 
       	1000);
        
       $(".file").change(function(){
       	if($(this).data("id")==0){
       		alert("请删除商品后再进行上传操作");
       		return false;
       	}
       })
        //  图片上传限制 
	

});