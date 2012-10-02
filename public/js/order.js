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

    // 添加物流信息
    $('#addLogisticsInfo').click(function() {
        load_logistics_form(1);
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
        console.log(logistic_company);
        var value = $(this).val();
        if(value) {
            for (var index in logistic_company) {
                if(value == index) {
                    for (var idx in logistic_company[index].method) {
                        option += '<option value="' + idx + '">' + logistic_company[index].method[idx] + '</option>';
                    };
                }
            };
        }

        $(this).parent().next().children('select').html(option);
    });

    // 展开产品物流信息表单
    $('.order_logistic').live('dblclick',function(){
        return; // 暂时不对产品级别设置
        $(this).next().toggle('slow');
    });

    // 导入文件
    $('#import_logistic').click(function() {
        var tips = $('#upload_tips');
        if($('#import_file').val()) {
            tips.html('<font style="color: blue">[上传中...]</font>');
            tips.fadeIn();
            $.ajaxFileUpload({
                url: '/order/ajax/import_logistic',
                secureuri: false,
                fileElementId: 'import_file',
                dataType: 'json',
                success: function( data, status ) {
                    var tips = $('#upload_tips');
                    if( typeof(data.error) != 'undefined') {
                        if(data.error != '') {
                            tips.html('<font style="color: red">[' + data.error + ']</font>');
                        } else {
                            tips.html('<font style="color: green">[' + data.msg + ']</font>');
                        }

                        tips.fadeIn();
                    }
                    tips.delay(2000).fadeOut();
                },
                error: function( data, status, e) {
                    var tips = $('#upload_tips');
                    tips.html('<font style="color: red">[' + e + ']</font>');
                    tips.fadeIn().delay(2000).fadeOut();
                    
                }
            });
        
        } else {
            tips.html('<font style="color: red">请先选择文件</font>');
            tips.fadeIn().delay(2000).fadeOut();
        }
    
    });
});

