$(function(){

    // 订单列表选项样式修改
    $('.tOptions2, .tOptions3').click(function () {
		$(this).toggleClass("act");
	});

    // 展开订单列表设置
    $('#order_list_options').click(function(){
        if($('#order_list_search').hasClass('act')) {
            $('#order_list_search').removeClass('act');
            $('#order_list .tablePars').slideUp(200);
        }
        $('#olist_search').hide();
        $('#olist_options').show();
        $('#order_list .tablePars').slideToggle(200);
    });

    // 展开订单列表搜索
    $('#order_list_search').click(function(){
        if($('#order_list_options').hasClass('act')) {
            $('#order_list_options').removeClass('act');
            $('#order_list .tablePars').slideUp(200);
        }
        $('#olist_options').hide();
        $('#olist_search').show();
        $('#order_list .tablePars').slideToggle(200);
    });
});
