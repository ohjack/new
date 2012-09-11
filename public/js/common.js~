$(function(){
    // 获取订单
    $('#getOrders').click(function() {
        $.ajax({
            type: "GET",
            url: "/order/spider",
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
        if(!window.confirm('你确认把所有未匹配物流的产品放到其他物流？')) {
            return ;
        }

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
                    $('#tips').html('<font style="color: green">转移成功!</font>');
                }
            },
            error: function(){
                $('#tips').html('<img src="/img/loading.gif" /><font style="color: blue">Loading...</font>');
            }
        });
    });
});

function closeTips() {
    setTimeout(function(){
        $('#tips').html('');
    }, 3000);
}
