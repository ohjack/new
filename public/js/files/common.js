$(function(){

    // 鼠标hover提示
    $('.tipS').tipsy({gravity: 's',fade: true, html:true});
    $('.tipN').tipsy({gravity: 'n',fade: true, html:true});

    // input 样式
    $("select, .check, .check :checkbox, input:radio, input:file").uniform();

    // 全屏
    $('.doFullscreen').toggle(function() {
        var target_id = $(this).attr('key');
        $('#'+target_id).addClass('fullscreen');
        $('#content').css('position', 'static');
        $('#sidebar').addClass('hide_important');
    }, function() {
        var target_id = $(this).attr('key');
        $('#'+target_id).removeClass('fullscreen');
        $('#content').css('position', 'relative');
        $('#sidebar').removeClass('hide_important');
    });

    // 展开表格列表
    $('.tOptions[ckey]').click(function(){

        var ckey = $(this).attr('ckey');

        if($(this).hasClass("act")) {
            $('.tablePars').slideUp(200);
        } else {
            $('.tablePars').slideUp(200, function(){
                $('.tOptions[ckey]').each(function(){
                    var this_ckey =  $(this).attr('ckey');
                    $('#' + this_ckey).hide();
                });
                $('#' + ckey).show();
            });
            $('.tablePars').slideDown(200);
        }

		$(this).toggleClass("act");
        $('.tOptions[ckey]').not(this).removeClass('act');
    });

    // tab 选项
	$.fn.contentTabs = function(){ 
	
		$(this).find(".tab_content").hide(); //Hide all content
		$(this).find("ul.tabs li:first").addClass("activeTab").show(); //Activate first tab
		$(this).find(".tab_content:first").show(); //Show first tab content
	
		$("ul.tabs li").click(function() {
			$(this).parent().parent().find("ul.tabs li").removeClass("activeTab"); //Remove any "active" class
			$(this).addClass("activeTab"); //Add "active" class to selected tab
			$(this).parent().parent().find(".tab_content").hide(); //Hide all tab content
			var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
			$(activeTab).show(); //Fade in the active content
			return false;
		});
	
	};
	$("div[class^='widget']").contentTabs(); //Run function on any div with class name of "Content Tabs"


    // 抓取订单
    $('#spider').click(function() {
        $.ajax({
            type: "GET",
            url: "/spider/order",
            dataType: "json",
            beforeSend: function() {
                $('#spider_items').hide();
                $('#spider_tips').dialog({
                    modal: true,
                    closeOnEscape: false,
                    draggable: false,
                    resizable: false,
                    buttons: {
                        "关闭": function() {
                              $(this).dialog("close");
                        }
                    },
                    close: function() {
                        window.location.reload(); 
                    }
                });
            },
            success: function(data) {
                $('#spider_orders').hide();
                if( data.status == 'success' ) {
                    var message = data.message;
                    if(message.new == 0) {
                        var tips = '没有新增订单，已同步' + message.rsync + '个订单';
                    } else {
                        var tips = '新增' + message.new + '个订单，已同步' + message.rsync + '个订单' ;
                    }
                    $('#spider_orders_info').html(tips);
                    spiderItems();
                } else if ( data.status == 'error') {
                    $('#spider_orders_info').html('<span style="color:#7D2A1C">' + data.message + '<span>');
                }
            },
            error: function() {
                $('#spider_orders').hide();
                $('#spider_orders_info').html('<span style="color:#7D2A1C">请求数据发生错误<span>');
            }
        });
    });
});


// 抓取产品
function spiderItems() {
    $.ajax({
        type: "GET",
        url: "/spider/item",
        dataType: "json",
        beforeSend: function() {
            $('#spider_items').show();
        },
        success: function(data) {
            $('#spider_items').hide();
            if( data.status == 'success' ) {
                var message = data.message;
                var tips = '';
                if(message.total == 0) {
                    tips = '没有抓取到任何产品';
                } else {
                    tips = '本次共抓取' + message.total + '个产品';
                }
                $('#spider_items_info').html(tips);
                $('#spider_success').show();
            } else if ( data.status == 'error') {
                $('#spider_items_info').html('<span style="color:#7D2A1C">' + data.message + '<span>');
            }
        },
        error: function() {
            $('#spider_items').hide();
            $('#spider_items_info').html('<span style="color:#7D2A1C">请求数据发生错误<span>');
        }
    });

}
