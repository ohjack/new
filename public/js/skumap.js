$(function(){

    $('.delete_skumap').click(function() {
        var id = $(this).attr('key');

        var options = { 
            id:id,
            _method: 'DELETE'
        }

        // ajax submit
        $.ajax({
            type: "POST",
            url: "/skumap/manage",
            data: options,
            dataType: "json",
            beforeSend: function() {
                $('#tips' + id).html('<img src="/img/loading.gif" /><font style="color: blue">Loading...</font>');
            },
            success: function(data){
                if(data == 'ok') {
                    $('#tips' + id).html('<font style="color: green;">删除成功!</font>');
                    $('#skumap' + id).fadeOut();
                } else if(data == 'error') {
                    $('#tips' + id).html('<font style="color: red">处理出错</font>');
                } else {
                    $('#tips' + id).html('<font style="color: red">未知错误</font>');
                }
            },
            error: function() {
                $('#tips' + id).html('<font style="color: red">提交错误</font>');
            }
        });
    
    });

    $('.update_skumap').click(function() {
        var id = $(this).attr('key');
        
        // check
        var original_sku   = $('input[name="original_sku' + id + '"]').val();
        var target_sku     = $('input[name="target_sku' + id + '"]').val();
        var product_name   = $('input[name="product_name' + id + '"]').val();
        var product_price  = $('input[name="product_price' + id + '"]').val();
        var logistics      = $('select[name="logistics' + id + '"] option:selected').val();

        if(original_sku.length < 1) {
            $('#tips' + id).html('<font style="color: red">表单数据有误</font>');
            return false;
        }

        if(target_sku.length < 1) {
            $('#tips' + id).html('<font style="color: red">请填写映射sku</font>');
            return false;
        }

        if(logistics.length < 1) {
            $('#tips' + id).html('<font style="color: red">请选择物流系统</font>');
            return false;
        } 

        var options = {
            id: id,
            product_name: product_name,
            product_price: product_price,
            original_sku: original_sku,
            target_sku: target_sku,
            logistics: logistics,
            _method: 'PUT'
        };

        // ajax submit
        $.ajax({
            type: "POST",
            url: "/skumap/manage",
            data: options,
            dataType: "json",
            beforeSend: function() {
                $('#tips' + id).html('<img src="/img/loading.gif" /><font style="color: blue">Loading...</font>');
            },
            success: function(data){
                if(data == 'ok') {
                    $('#tips' + id).html('<font style="color: green;">更新成功!</font>');
                } else if(data == 'error') {
                    $('#tips' + id).html('<font style="color: red">处理出错</font>');
                } else {
                    $('#tips' + id).html('<font style="color: red">未知错误</font>');
                }
            },
            error: function() {
                $('#tips' + id).html('<font style="color: red">提交错误</font>');
            }
        });
    });
});
