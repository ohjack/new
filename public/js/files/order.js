$(function(){

    // 更新列
    $('#olist_fields :checkbox').live('click',function(){
        var colname = $(this).parent().parent().next().text();
        if($(this).attr('checked')) {
            colnums[colnums.length] = colname;
        } else {
            for (var index in colnums) {
                if(colnums[index] == colname) {
                    colnums.splice(index,1);
                }
            }
        }

        // ajax 更新用户配置
        var fields = '';
        var dot = '';
        $('#olist_fields :checkbox').each(function(){
            if($(this).attr('checked')) {
                fields += dot + $(this).attr('id');
                dot = ',';
            }
        });
        $.ajax({
            url: '/order/ajax/setting',
            data: {fields:fields}
        });

        // 表格重画
        oTable.fnDraw();
    });

});

// 初始化列实现选项
function init_options() {
    if(!$('#olist_fields').html()) {
        $('#olist_fields').html($('#olist_fieds_hide').html());
        $('#olist_fieds_hide').remove();
        $('#olist_fields').children('.fields').addClass('check');
        $("#olist_fields .check :checkbox").uniform();
    }
}


