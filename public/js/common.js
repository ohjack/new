// 重载页面
function reload() {
    window.location.reload(); 
}

/**
 * 构造加载提示
 *
 * param text string  提示文字
 * param mask boolean 是否启用遮罩
 *
 */
function show_tips(text, mask) {

    clear_all_tips();

    var loading_box = '<div class="loading"><img src="/img/loading.gif">' + text + '</div>';
    var mask_box    = '<div class="mask"></div>';

    var container = !mask ? loading_box : mask_box + loading_box;
    
    $('body').append(container); 
    $('.mask, .loading').fadeIn();

}

// 清除所有交互提示
function clear_all_tips() {
    $('.loading, .tips, .mask').remove();
}

// 抓取产品
function spiderItems() {
    $.ajax({
        type: "GET",
        url: "/spider/item",
        dataType: "json",
        beforeSend: function() {
            $('.mask').fadeIn();
            $('.loading').fadeIn();
        },
        success: function(data) {
            if( data.status == 'success' ) {
                var message = data.message;
                var tips = '';
                if(message.total == 0) {
                    tips = '<br/>没有抓取到任何产品';
                } else {
                    tips = '<br/>抓取产品:' + message.total + '个<br/>新增产品:' +message.insert+ '个<br/>更新产品:' + message.update + '个';
                }
                html = $('.loading').html().replace('<img src="/img/loading.gif"> 继续产品抓取中... ', '');
                $('.loading').html(html + '<span style="color: green">产品抓取成功!' + tips+ '<br />抓取全部完成</span>');
            } else if ( data.status == 'error') {
                $('.loading').append('<br /><span style="color: red">' + data.message + '</span>');
            }
        },
        error: function() {
            $('.loading').append('<br /><span style="color: red">请求数据时发生错误! </span>');
        }
    });
};

$(function(){

    // 小提示关闭按钮
    $('em.click').live('click', function() {
        $(this).parent().parent().fadeOut();
        $('.mask').fadeOut();
        setTimeout("reload()", 1000);
        
    });

    // 选项卡效果
    $('.tab ul > li').click(function(){
        var target = $(this).attr('panel');
        $(this).parent().children('li').each(function(){
            var this_panel = $(this).attr('panel');
            if(this_panel == target) {
                $('#' + this_panel).show();
                $(this).addClass('tab_current');
            } else {
                $('#' + this_panel).hide();
                $(this).removeClass('tab_current');
            }
        });

    });

    // 抓取订单
    $('#spiderOrders').click(function() {

        $.ajax({
            type: "GET",
            url: "/spider/order",
            dataType: "json",
            beforeSend: function() {
                $('.mask').fadeIn();
                $('.loading').fadeIn();
            },
            success: function(data) {
                if( data.status == 'success' ) {
                    var message = data.message;
                    if(message.total == 0) {
                        var tips = '<br/>没有抓取到任何订单。<br/>没有新订单或者与上次抓取间隔间隔不到两分钟。';
                    } else {
                        var tips = '<br/>抓取订单:' + message.total + '个<br/>新增订单:' +message.insert+ '个<br/>更新订单:' + message.update + '个';
                    }
                    $('.loading').html('<span style="color: green"><em class="click">[ 关闭 ]</em>订单抓取成功!' + tips+ '</span>' + '<br / ><img src="/img/loading.gif"> 继续产品抓取中... ');
                    spiderItems();
                } else if ( data.status == 'error') {
                    $('.loading').html('<span style="color: red"><em class="click">[ 关闭 ]</em>' + data.message + '</span>');
                }
            },
            error: function() {
                $('.loading').html('<span style="color: red"><em class="click">[ 关闭 ]</em>请求数据时发生错误! </span>');
            }
        });
    });

    // 匹配物流 
    $('#matchLogistics').click(function(){
        var option = {};

        $.ajax({
            type: "POST",
            url: "/order/logistics",
            dataType: "json",
            data: option,
            beforeSend: function() {
                $('.mask').fadeIn();
                $('.loading').fadeIn();
            },
            success: function(data) {
                if(data.status == 'success') {
                    var message = data.message;
                    if(message.total == '0') {
                        var tips = '<br/>没有匹配到的订单';
                    } else {
                        var tips = '<br/>共匹配' + message.total +'个';
                    }
                    $('.loading').html('<span style="color: green"><em class="click">[ 关闭 ]</em>匹配成功!' + tips+ '</span>');
                }
            },
            error: function() {
                $('.loading').html('<span style="color: red"><em class="click">[ 关闭 ]</em>请求数据时发生错误! </span>');
            }
        });
    });

    // 物流处理
    $('#handleLogistics').click(function() {

        var option = {};

        $.ajax({
            type: "GET",
            url: '/order/logistics',
            dataType: 'json',
            data: option,
            beforeSend: function() {
                $('.mask').fadeIn();
                $('.loading').fadeIn();
            },
            success: function(data) {
                if(data.status == 'success') {
                    var tips = '';
                    var message = data.message;
                    var total = message.length;
                    if( total > 0) {
                        tips = '目前此次操作有' + total + '个物流文件';
                        for (var i = 0; i < total; i++) {
                            tips += '<br /><a target="_blank" href="/data/logistics_file/' + message[i].filename +'">' + message[i].name + '点击下载 [ ' + message[i].total + '条记录 ] </a>';
                        };
                    } else {
                        tips = '没有需要处理的物流';
                    }

                    $('.loading').html('<span style="color: green"><em class="click">[ 关闭 ]</em>' + tips + '</span>');
                
                }
            },
            error: function() {
                $('.loading').html('<span style="color: red"><em class="click">[ 关闭 ]</em>请求数据时发生错误! </span>');
            }
        });
    });
});
