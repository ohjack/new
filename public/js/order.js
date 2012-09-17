$(function(){

    $('.order').dblclick(function(){
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
                    for (var i = 0; i < items_count; i++) {
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

    $('#search_order').click(function(){
        $('.mask').fadeIn();
        $('.search_order').fadeIn();
        $('.search_order > .title').children('em').click(function(){
            $('.mask').fadeOut();
            $('.search_order').fadeOut();
        });
    });

    // select all
    $('input[name="selectAll"]').click(function(){
       if($(this).attr('checked') == 'checked') {
            $('input[name="id[]"]').attr('checked', 'checked');
       } else {
            $('input[name="id[]"]').removeAttr('checked');
       }
    }); 

    // sku map form hide
    $('#close').click(function(){
        $('#addSkuMap').fadeOut();
    });

    // sku map submit
    $('#skuMapSubmit').click(function(){
        
        // check
        var original_sku = $('input[name="original_sku"]').val();
        var target_sku = $('input[name="target_sku"]').val();
        var logistics = $('select[name="logistics"] option:selected').val();

        if(original_sku.length < 1) {
            $('#skuTips').html('<font style="color: red">请填写原sku</font>');
            return false;
        }

        if(target_sku.length < 1) {
            $('#skuTips').html('<font style="color: red">请填写目标sku</font>');
            return false;
        }

        if(logistics.length < 1) {
            $('#skuTips').html('<font style="color: red">请选择系统</font>');
            return false;
        } 

        var options = {
            original_sku: original_sku,
            target_sku: target_sku,
            logistics: logistics
        };

        // ajax submit
        $.ajax({
            type: "POST",
            url: "/skumap",
            data: options,
            dataType: "json",
            beforeSend: function() {
                $('#skuTips').html('<img src="/img/loading.gif" /><font style="color: blue">Loading...</font>');
            },
            success: function(data){
                if(data == 'ok') {
                    $('#skuTips').html('<font style="color: green;">映射成功!</font>');
                    closeSkuMap();
                } else if(data == 'exists') {
                    $('#skuTips').html('<font style="color: red">映射已存在</font>');
                    closeSkuMap();
                } else if(data == 'error') {
                    $('#skuTips').html('<font style="color: red">错误请检查提交内容</font>');
                } else {
                    $('#skuTips').html('<font style="color: red">未知错误</font>');
                }
            },
            error: function() {
                $('#skuTips').html('<font style="color: red">提交错误</font>');
            }
        });
    });
});

function closeSkuMap() {
    setTimeout(function(){
        $('#addSkuMap').fadeOut();
    }, 1000);
}

