$(function(){
    // 获取订单
    $('#spiderOrders').click(function() {
        $.ajax({
            type: "GET",
            url: "/spider/order",
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
    $('#matchLogistics').click(function(){
        var option = {};

        $.ajax({
            type: "POST",
            url: "/order/logistics",
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
});
