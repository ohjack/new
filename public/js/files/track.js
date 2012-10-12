$(function(){
    // 导入文件
    $('#import_logistic').live('click', function() {
        if($('#import_file').val()) {
            $.jGrowl('文件上传中...');
            $.ajaxFileUpload({
                url: '/order/ajax/import_logistic',
                secureuri: false,
                fileElementId: 'import_file',
                dataType: 'json',
                success: function( data, status ) {
                    var tips = $('#upload_tips');
                    if( typeof(data.error) != 'undefined') {
                        if(data.error != '') {
                            $.jGrowl('文件上传失败：' + data.error);
                        } else {
                            $.jGrowl(data.msg);
                        }
                    }
                },
                error: function( data, status, e) {
                    $.jGrowl(e);
                }
            });
        
        } else {
             $.jGrowl('请先选择文件');
        }
    });
}) 
