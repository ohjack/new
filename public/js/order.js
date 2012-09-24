$(function(){

    // 订单详情
    $('.order').dblclick(function() {
        var id = $(this).attr('key');
        $.ajax({
            type: 'GET',
            url:  '/order/ajax/info?order_id=' + id,
            dataType: 'json',
            beforeSend: function() {
                $('.mask').fadeIn();
                $('.loading').fadeIn();
            },
            success: function( data ) {
                $('.loading').hide();
                $('.order_detail').fadeIn();

                // 订单信息
                $('#order_info td').each(function() {
                    var field = ($(this).attr('field'));
                    if(field == 'shipping_address') {
                        $(this).html(eval('data.shipping_address3 + \' \' + data.shipping_address2 + \' \' + data.shipping_address1'));
                    } else if (field == 'total') {
                        $(this).html(eval('data.currency + \' \' + data.total'));
                    } else {
                        $(this).html(eval('data.'+ field));
                    }
                });

                // 产品列表
                var items_count = data.items.length;
                var item_row = '';
                if( items_count > 0) {
                    for ( var i = 0; i < items_count; i++ ) {
                        item_row += '<tr>' + 
                                    '<td>' + data.items[i].entry_id + '</td>' + 
                                    '<td>' + data.items[i].name + '</td>' + 
                                    '<td>' + data.items[i].sku + '</td>' + 
                                    '<td>' + data.items[i].currency + ' ' + data.items[i].price + '</td>' + 
                                    '<td>' + data.items[i].quantity + '</td>' + 
                                    '<td>' + data.items[i].shipping_currency + ' ' + data.items[i].shipping_price + '</td>' + 
                                    '</tr>';
                    };
                } else {
                    item_row = '<tr><td colspan="6">没有产品</td></tr>';
                }
                $('#items_list > tbody').html(item_row);

                // 产品标识
                var marks_count = data.marks.length;
                var marks_row = '';
                if( marks_count > 0) {
                    for ( var i = 0; i < marks_count; i++ ) {
                        marks_row += '<li mark_id="' + data.marks[i].id + '" style="color: ' + data.marks[i].color + '">' + data.marks[i].name + '<a href="javascript:;">x</a></li>';
                    }
                }
                $('#mark').html(marks_row);

                // 赋值Order ID
                $('#add_mark').attr('order_id', id);

                // 关闭
                $('.order_detail > .title').children('em').click(function(){
                    $('.mask').fadeOut();
                    $('.order_detail').fadeOut();
                });
            },
            error: function () {
                $('.loading').html('<span style="color: red"><em class="click">[ 关闭 ]</em>请求数据时发生错误! </span>');
            }
        });
        
    });

    // 确认订单
    $('#confirm_order').click(function() {
        $('.mask').fadeIn();
        $('.confirm_order').fadeIn();

        // 关闭
        $('.confirm_order > .title').children('em').click(function(){
            $('.mask').fadeOut();
            $('.confirm_order').fadeOut();
        });
    });

    // 确认订单提交
    $('#confirm_order_submit').click(function() {

        $.ajax({
            type: 'POST',
            url: '/order/ajax/confirm',
            success: function( data ) {
                console.log(data);
            }
        
        });
    });

    // 添加物流信息
    $('#addLogisticsInfo').click(function() {
        load_logistics_form(0);
        // 关闭
        $('.add_logistics_info > .title').children('em').click(function(){
            $('.mask').fadeOut();
            $('.add_logistics_info').fadeOut();
        });
    });

    // 添加订单标识
    $('#add_mark').click(function(){
        var mark_select = $(this).prev();
        var mark_id = mark_select.val();
        var order_id = $('#add_mark').attr('order_id');
        var mark_color = mark_select.children(':selected').attr('color');
        var mark_name = mark_select.children(':selected').html();

        var data = {
            mark_id: mark_id,
            order_id: order_id
        }

        $.ajax({
            type: "POST",
            url: "/order/ajax/addmark", 
            dataType: "json",
            data: data,
            beforeSend: function() {
                $('.mask').fadeIn();
                $('.loading').fadeIn();
            },
            success: function( data ) {
                if( data == 'ok' ) {
                    $('#mark').prepend('<li mark_id="' + mark_id + '" style="color:' + mark_color + '">' + mark_name + '<a href="javascript:;">x</a></li>');
                    $('.loading').fadeOut();
                }
                $('.mask').fadeOut();
            },
            error: function() {
                $('.loading').html('<span style="color: red">请求数据时发生错误! </span>');
                $('.loading').fadeOut();
            }

        });

    });

    // 删除订单标识
    $('#mark a').live('click', function() {

        var li = $(this).parent();
        var mark_id  = li.attr('mark_id');
        var order_id = $('#add_mark').attr('order_id');

        var data = {
            mark_id: mark_id,
            order_id: order_id
        }

        $.ajax({
            type: "POST",
            url: "/order/ajax/delmark",
            dataType: "json",
            data: data,
            beforeSend: function() {
                $('.mask').fadeIn();
                $('.loading').fadeIn();
            },
            success: function( data ) {
                if( data == 'ok') {
                    li.remove();
                    $('.loading').fadeOut();
                }
                $('.mask').fadeOut();
            },
            error: function() {
                $('.loading').html('<span style="color: red">请求数据时发生错误! </span>');
                $('.loading').fadeOut();
            }
        });
    });

    // 订单列表页弹出批量订单标识设置
    $('#mark_setting_button').toggle(function(){
        $(this).removeClass('close').addClass('open');
        $('#mark_setting').slideDown();
    }, function(){
        $(this).removeClass('open').addClass('close');
        $('#mark_setting').slideUp();
    });

    // 批量添加标识
    $('#add_marks').click(function() {
        var mark_ids = new Array();
        $("input[name='mark_id[]']:checked").each(function() {
            var val = $(this).val();
            mark_ids.push(val);
        });

        if(mark_ids.length < 1) {
            alert('请先选择标识');
            return;
        } 

        var order_ids = new Array();
        $("input[name='id[]']:checked").each(function() {
            var val = $(this).val();
            order_ids.push(val);
        });

        if( order_ids.length < 1) {
            alert('请先选择订单');
            return;
        }
    
        var data = {order_ids: order_ids, mark_ids: mark_ids};
        $.ajax({
            type: 'POST',
            url: '/order/ajax/setmarks',
            data: data,
            success: function( data ) { 
                if( data == 'ok') reload();
            }
        });
    
    });

    // 订单搜索框
    $('#search_order').click(function(){
        $('.mask').fadeIn();
        $('.search_order').fadeIn();
        $('.search_order > .title').children('em').click(function(){
            $('.mask').fadeOut();
            $('.search_order').fadeOut();
        });
    });

    // 全选
    $('input[name="select_all"]').click(function(){
       if($(this).attr('checked') == 'checked') {
            $('input[name="id[]"]').attr('checked', 'checked');
       } else {
            $('input[name="id[]"]').removeAttr('checked');
       }
    }); 

    // 物流公司切换联动运输方式
    $('.logistic_company').live('change', function(){
        var option = '<option value>--请选择--</option>';

        if($(this).val()) {
            var company = $(this).children('option:selected').attr('key');
            var method = logistic[company].method;

            for (var index in method) {
                option += '<option value="' + index + '">' + method[index] + '</option>';

            };
        }

        $(this).parent().next().children('select').html(option);
    });

    // 展开产品物流信息表单
    $('.order_logistic').live('dblclick',function(){
            $(this).next().toggle('slow');
    });

    // 提交物流表单搜索
    $('#logistic_search').click(function() {
        var entry_id = $(this).prev().val();
        load_logistics_form(entry_id);
    });

    // 提交物流信息
    $('#logistic_submit').click(function(){
        var data = $('#logistic_form').serialize();

        $.ajax({
            type: 'POST',
            url: '/shipping',
            data: data,
            dataType: 'json',
            beforeSend: function() {
            	$('.mask').fadeIn();
                $('.loading').fadeIn();
           
            },
            success: function() {
            	$('.loading').fadeOut();
            	var pnum=$('#paginator > .selected').text();
            	pnum=(pnum>0)?pnum:1;
            	load_logistics_form(pnum);
            },
            error: function() {
            
            }
        });
    });
});

// 加载物流信息表单
function load_logistics_form ( page ) {
    $('.mask').fadeIn();
    $('.add_logistics_info').fadeIn();

    if(isNaN(page)) {
        var option = {page: 1, entry_id: page}
        page = 0;
    } else {
        var option = {page: page + 1};
    }

    $.ajax({
        type: 'GET',
        url: '/order/ajax/matched',
        data: option,
        dataType: "json",
        beforeSend: function() {
        
        },
        success: function( data ) {
            if( data.status == 'success') {
                var message = data.message;
                logistic = data.logistic;
                var total = message.length;

                // 物流公司
                var logistic_company = '<option value>--请选择--</option>';
                for (var i = 0; i < logistic.length; i++) {
                    logistic_company += '<option value="' + logistic[i].name + '" key="' + i + '">' + logistic[i].name + '</option>';
                };

                // 运输方式
                var logistic_method = '<option value>--请选择--</option>';
                var order_list = '';
                for (var i = 0; i < total; i++) {
                    var id = message[i].id;
                    var items = message[i].items;
                    var item_list = '';
                    for (var j = 0; j < items.length; j++) {
                        item_list += '<tr>' +
                                        '<td>' + items[j].entry_id + '</td>' +
                                        '<td>' + items[j].sku + '</td>' +
                                        '<td><input name="logistic[' + id + '][items][' + items[j].id + '][ship_quantity]" style="width:30px" type="text" value="' + items[j].quantity + '" />/'+items[j].quantity+'</td>' +
                                        '<td><select name="logistic[' + id + '][items][' + items[j].id + '][company]" style="width:80px" class="logistic_company">' + logistic_company + '</select></td>' +
                                        '<td><select name="logistic[' + id + '][items][' + items[j].id + '][method]" style="width:80px" class="logistic_method">' + logistic_method + '</select></td>' +
                                        '<td><input name="logistic[' + id + '][items][' + items[j].id + '][tracking_no]" type="text" /><input name="logistic[' + id + '][items][' + items[j].id + '][quantity]" type="hidden" value="'+items[j].quantity+'"></td>' + 
                                     '</tr>';
                    };
                    order_list += '<tr key="' + id + '" class="order_logistic" title="双击展开产品">' +
                                    '<td>' + message[i].entry_id + '</td>' +
                                    '<td>' +
                                        '<select style="width: 80px" name="logistic[' + id + '][company]" class="logistic_company">' + logistic_company + '</select>' +
                                    '</td>' + 
                                    '<td><select style="width: 80px" name="logistic['+ id +'][method]" class="logistic_method">' + logistic_method + '</select></td>' + 
                                    '<td><input name="logistic[' + id + '][tracking_no]" type="text"/></td>' +
                                    '<td><input name="logistic[' + id + '][ship_first]" type="checkbox"></td>' +
                                 '</tr>' + 
                                 '<tr style="display:none">' +
                                    '<td colspan="5" style="background: #fee">' +
                                        '<table style="margin: 0" class="item_logistic">' +
                                            '<thead>' +
                                                '<tr>' +
                                                    '<th>产品ID</th>' +
                                                    '<th>Sku</th>' +
                                                    '<th style="width:60px">发货数量</th>' +
                                                    '<th>物流公司</th>' +
                                                    '<th>物流方式</th>' +
                                                    '<th>跟踪号</th>' +
                                            '</thead>' +
                                            '<tbody>' + item_list + '</tbody>' +
                                        '</table>' +
                                    '</td>' +
                                 '</tr>';
                };
                $('#add_logistics > tbody').html(order_list);

                pages = Math.ceil(data.total / data.per_page);
                generateRows(page);
            }
        },
        error: function() {
            alert('error');
        }
    });
}


// 提交物流跟踪生成页码
function generateRows(selected) {
	
	if (pages <= 5) {
        var pagers = "<div id='paginator'>";
        for (var i = 0; i < pages; i++) {
            var page = i+1;
            if(i == selected)
                pagers += "<a href='javascript:;' class='pagor selected'>" + page + "</a>";
            else
                pagers += "<a href='javascript:;' class='pagor'>" + page + "</a>";
        };

        pagers += "<div style='clear:both;'></div></div>";

        $(".pagination").html(pagers);
		$(".pagor").click(function() {
			var index = $(".pagor").index(this);
            if(index == selected) return;
            load_logistics_form (index)
			$(".pagor").removeClass("selected");
			$(this).addClass("selected");
		});		
	} else {
		if (selected < 5) {
			// Draw the first 5 then have ... link to last
			var pagers = "<div id='paginator'>";
			for (i = 1; i <= 5; i++) {
				if (i == selected) {
					pagers += "<a href='javascript:;' class='pagor selected'>" + i + "</a>";
				} else {
					pagers += "<a href='javascript:;' class='pagor'>" + i + "</a>";
				}				
			}
			pagers += "<div style='float:left;padding-left:6px;padding-right:6px;'>...</div><a href='javascript:;' class='pagor'>" + Number(pages) + "</a><div style='clear:both;'></div></div>";
			
			$("#paginator").remove();
			$(".pagination").html(pagers);
			$(".pagor").click(function() {
				updatePage(this);
			});
		} else if (selected > (Number(pages) - 4)) {
			// Draw ... link to first then have the last 5
			var pagers = "<div id='paginator'><a href='javascript:;' class='pagor'>1</a><div style='float:left;padding-left:6px;padding-right:6px;'>...</div>";
			for (i = (Number(pages) - 4); i <= Number(pages); i++) {
				if (i == selected) {
					pagers += "<a href='javascript:;' class='pagor selected'>" + i + "</a>";
				} else {
					pagers += "<a href='javascript:;' class='pagor'>" + i + "</a>";
				}				
			}			
			pagers += "<div style='clear:both;'></div></div>";
			
			$("#paginator").remove();
			$(".pagination").html(pagers);
			$(".pagor").click(function() {
				updatePage(this);
			});		
		} else {
			// Draw the number 1 element, then draw ... 2 before and two after and ... link to last
			var pagers = "<div id='paginator'><a href='javascript:;' class='pagor'>1</a><div style='float:left;padding-left:6px;padding-right:6px;'>...</div>";
			for (i = (Number(selected) - 2); i <= (Number(selected) + 2); i++) {
				if (i == selected) {
					pagers += "<a href='javascript:;' class='pagor selected'>" + i + "</a>";
				} else {
					pagers += "<a href='javascript:;' class='pagor'>" + i + "</a>";
				}
			}
			pagers += "<div style='float:left;padding-left:6px;padding-right:6px;'>...</div><a href='javascript:;' class='pagor'>" + pages + "</a><div style='clear:both;'></div></div>";
			
			$("#paginator").remove();
			$(".pagination").html(pagers);
			$(".pagor").click(function() {
				updatePage(this);
			});			
		}
	}
}

// 更新分页页面
function updatePage(elem) {
	// Retrieve the number stored and position elements based on that number
	var selected = $(elem).text();

	// First update content
    load_logistics_form (selected)
	
	// Then update links
	//generateRows(selected);
}
