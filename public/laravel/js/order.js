$(function(){

    // ajax get items
    $('.openItem').click(function(){
        var id = $(this).attr('key');
        var items = $('#items' + id);

        if(items.css('display') == 'none') {
            items.fadeIn();
            $(this).children('.open').removeClass('open').addClass('close');
        } else {
            items.fadeOut();
            $(this).children('.close').removeClass('close').addClass('open');
        }

        $.ajax({
            type: "GET",
            url: "/item?order_id=" + id,
            dataType: "json",
            beforeSend: function() {
                $('#show_items_' +id).html('<tr><td colspan="6" style="text-align:center;"><img src="/img/loading.gif" />Loading...</td></tr>');
            }
        }).done(function(data){
            var html = '<tr>';
            for(var i = 0; i < data.length; i++) {
                html += '<td>#' + data[i].id + '</td>' +
                        '<td>' + data[i].name + '</td>' +
                        '<td class="sku">' + data[i].sku + '</td>' + 
                        '<td>' + data[i].price + '</td>' +
                        '<td>' + data[i].quantity + '</td>' +
                        '<td>' + data[i].shipping_price + '</td>'
            }
            html + '<tr>';

            $('#show_items_' + id).html(html);

            // add sku map
            $('.sku').click(function() {
                $('#addSkuMap').fadeIn();
                $('input[name="original_sku"]').attr('value', $(this).text());
                $('input[name="target_sku"]').attr('value', '');
                $('select[name="logistics"]').children('option').removeAttr('selected');
                $('#skuTips').html('');
            });
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
            url: "/sku_map",
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


    // 获取订单
    $('#getOrders').click(function(){
        $.ajax({
            type: "GET",
            url: "/test",
            dataType: "json",
            beforeSend: function() {
                $('#tips').html('<img src="/img/loading.gif" /><font style="color: blue">Loading...</font>');
            },
            success: function(data) {
                if( data.status == 'success' ) {
                    $('#tips').html('<font style="color: green">抓取成功!</font>');
                    closeTips();
                } else if ( data.status == 'error') {
                    $('#tips').html('<font style="color: red">' + data.message + '</font>');
                    closeTips();
                }
            },
            error: function() {
                $('#tips').html('<font style="color: red">提交错误</font>');
                closeTips();
            }
        });
    });

    // match logistics
    $('#logistics').click(function(){
        var option = {
            action: 'allOrder'
        };

        $.ajax({
            type: "POST",
            url: "/item/logistics",
            dataType: "json",
            data: option,
            beforeSend: function() {
                $('#tips').html('<img src="/img/loading.gif" /><font style="color: blue">Loading...</font>');
            },
            success: function(data) {
                if(data == 'ok') {
                    $('#tips').html('<font style="color: green">匹配成功!</font>');
                }
            },
            error: function() {
                $('#tips').html('<img src="/img/loading.gif" /><font style="color: blue">Loading...</font>');
            }
        });
    });

    // put the order to other logistics
    $('#allOther').click(function(){
        var option = {
            action: 'allOther'
        };
        $.ajax({
            type: "POST",
            url: "/item/logistics",
            dataType: "json",
            data: option,
            beforeSend: function() {
                $('#tips').html('<img src="/img/loading.gif" /><font style="color: blue">Loading...</font>');
            },
            success: function(data) {
                if(data == 'ok') {
                    $('#tips').html('<font style="color: green">匹配成功!</font>');
                }
            },
            error: function(){
                $('#tips').html('<img src="/img/loading.gif" /><font style="color: blue">Loading...</font>');
            }
        });
    });
    

});

function closeSkuMap() {
    setTimeout(function(){
        $('#addSkuMap').fadeOut();
    }, 1000);
}

function closeTips() {
    setTimeout(function(){
        $('#tips').html('');
    }, 3000);
}
