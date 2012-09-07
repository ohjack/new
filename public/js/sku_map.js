$(function(){


    // sku map submit
    $('.sku_map_submit').click(function(){
        var id = $(this).attr('key');
        
        // check
        var original_sku = $('input[name="original_sku' + id + '"]').val();
        var target_sku = $('input[name="target_sku' + id + '"]').val();
        var logistics = $('select[name="system' + id + '"] option:selected').val();

        if(original_sku.length < 1) {
            $('#tips' + id).html('<font style="color: red">原sku为空</font>');
            return false;
        }

        if(target_sku.length < 1) {
            $('#tips' + id).html('<font style="color: red">请填写目标sku</font>');
            return false;
        }

        if(logistics.length < 1) {
            $('#tips' + id).html('<font style="color: red">请选择系统</font>');
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
                $('#tips' + id).html('<img src="/img/loading.gif" /><font style="color: blue">Loading...</font>');
            },
            success: function(data){
                if(data == 'ok') {
                    $('#tips' + id).html('<font style="color: green;">映射成功!</font>');
                    $('#item'+ id).fadeOut('slow');
                } else if(data == 'exists') {
                    $('#tips' + id).html('<font style="color: red">映射已存在</font>');
                    $('#item'+ id).fadeOut().remove();
                } else if(data == 'error') {
                    $('#tips' + id).html('<font style="color: red">错误请检查提交内容</font>');
                } else {
                    $('#tips' + id).html('<font style="color: red">未知错误</font>');
                }
            },
            error: function() {
                $('#tips' + id).html('<font style="color: red">提交错误</font>');
                    close(id);
            }
        });
    });


});

function close(id) {
    alert(id);
    setTimeout(function(id){
        alert(id);
        $('#item'+id).fadeOut();
    }, 1000);
}
