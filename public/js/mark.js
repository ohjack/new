$(function(){
	//添加输入框
	$("#add").click(function(){
		var i=$("#mark tbody tr:last").index();
		var addstr=$("#mark tbody tr:last").clone().html();
		addstr=addstr.replace(/\[\d{1,}\]/g,"["+i+"]");
		$("#mark tbody tr:last").after("<tr>"+addstr+"</tr>");
	});
	//减少输入框
	$("#subtract").click(function(){
		var i=$("#mark tbody tr:last").index();
		var tag=$("#tag_add").index();
		if(i>tag)
			{
				$("#mark tbody tr:last").remove();
			}
	});
	
	//全选,全不选
	$("#select_all").click(function(){
		if($(this).attr('checked')=='checked')
			{
				$("input[name^='mark']").attr('checked','checked');
			}
		else
			{
				$("input[name^='mark']").removeAttr('checked');
			}
	});
	
	//删除所选
	$("#delete").click(function(){
		var ids='';
		$("input[name^='mark']:checked").each(function(){
			ids+=$(this).val()+',';
		});
		ids=ids.replace(/,$/g,'');
		$.ajax({
			type:'GET',
			url:'mark/delete?mark_ids='+ids,
			success:function(data){
				window.location.reload();
			},
			
		});
	});
	
	//选取颜色
	//===== Color picker =====//

	$('.cPicker').live('click',function(){

		$(this).die('click');

		$(".cPicker").ColorPicker({
			color: '#e62e90',
			onBeforeShow:function(){MyColorPickerDivClicked=$(this);},
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				//console.log(hex);
				MyColorPickerDivClicked.find("div").css('backgroundColor', '#' + hex);
				MyColorPickerDivClicked.find('input').val("#"+hex);

				
			}
		});
		
		$(this).click();
	});


	/*
	$('.cPicker').ColorPicker({
		color: '#e62e90',
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('.cPicker div').css('backgroundColor', '#' + hex);
			$(this).children('input').val(hex);
			console.log($(this).html());

			
		}
	});
	*/
});