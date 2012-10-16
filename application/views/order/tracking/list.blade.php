@layout('layout')
@section('script')
{{ HTML::script('js/ajaxfileupload.js') }}
{{ HTML::script('js/files/track.js') }}
@endsection
@section('sidebar')
    @include('sidebar')
@endsection
@section('content')
<!-- content begins -->
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-target-2"></span>跟踪订单</span>
        @render('tinfo')
    </div>
    
    <!-- Breadcrumbs line -->
    <div class="breadLine">
        <div class="bc">
            <ul id="breadcrumbs" class="breadcrumbs">
                <li><a href="{{ URL::base() }}">控制中心</a></li>
                <li><a href="{{ URL::base() }}/order/tracking">跟踪订单</a></li>
            </ul>
        </div>
    </div>

    <!-- main content-->
    <div class="wrapper">
        <div class="widget fluid" id="tlist">
            <div class="whead"><h6>跟踪信息</h6><div class="clear"></div></div>
            <div id="track_list" class="hiddenpars">
                <div class="cOptions">
                <a href="javascript:;" class="tOptions tipS doFullscreen" key="tlist" title="全屏"><img src="{{URL::base()}}/images/icons/fullscreen" alt=""/></a>
                <a href="javascript:;" ckey="tlist_search" class="tOptions tipS" title="搜索"><img src="{{URL::base()}}/images/icons/search" alt=""/></a>
                <a href="javascript:;" ckey="tlist_options" class="tOptions tipS" title="设置"><img src="{{URL::base()}}/images/icons/options" alt=""/></a>
                <a href="javascript:;" ckey="tlist_import" class="tOptions tipS" title="导入"><img src="{{URL::base()}}/images/icons/import" alt=""/></a>
                </div>
            <!--div class="formRow">
                {{ Form::open('order/tracking', 'GET') }}
                <span style="float: right;">订单ID：<input name="entry_id" value=""> <input type="submit" value="搜索"></span>
                {{ Form::close() }}
               
            </div-->
            <div id="tlist_import_hide" style="display:none">
                <form name="form" action="" method="POST" enctype="multipart/form-data">
                    <div class="formRow" style="border: 0">
                        导入文件:<input id="import_file" name="import_file" type="file"><input class="buttonS bBlue ml10" type="button" value="上传" id="import_logistic"><span style="display: none" id="upload_tips"></span><a class="buttonS bBlue ml10" href="{{Route::controller('home')}}/data/demo/shipped.xls">下载导入模板</a>
                    </div>
                    <span class="clear"></span>
                </form>
            </div>
            {{ Form::open('shipping') }}
            <table class="tDefault formRow" style="width: 100%" id="tracking_table">
              <thead>
                <tr>
                    <th key="order_entry_id"  width="150px">订单ID</th>
                    <th width="120px">物流公司</th>
                    <th>物流方式</th>
                    <th>跟踪号(提前发货请填Letter)</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="5" class="textR"><input type="submit" class="buttonS bBlue" value="保存"/></td>
                </tr>
              </tfoot>
            </table>
        {{ Form::close() }}
        <script type="text/javascript">
            $(function(){
                // 表格初始化
                oTable = $('#tracking_table').dataTable({
                    "bSort": false,
                    "bProcessing" : true,
                    "bFilter": true,
                    "bServerSide": true,
                    "bJQueryUI": false,
                    "bAutoWidth": true,
                    "sPaginationType": "full_numbers",
                    "sAjaxSource": "/order/ajax/tracking",
                    "sDom": '<"H"<"#tlist_options"<"formRow"l<"clear">>><"#tlist_search"<"#filter_tlist_order_entry_id">><"#tlist_import">>tr<"F"ip>',
                    "fnInitComplete": function() {
                        $('#tlist_import').html($('#tlist_import_hide').html());
                        $('#tlist_import_hide').remove();

                        $('#tlist_search div').each(function(){
                            var filter = $(this);
                            var key = filter.attr('id').replace('filter_tlist_', '');
                            $('#tracking_table').find('th').each(function() {
                                if($(this).attr('key') == key) {
                                    var filter_name = $(this).html();
                                    filter.find('span').addClass('grid3');
                                    filter.prepend('<div class="grid2" style="line-height: 26px">' + filter_name + ':</div>');
                                    filter.append('<div class="clear"></div>');
                                    filter.parent().addClass('formRow').css('border', '0');
                                }
                            });
                        });
                    }
                }).columnFilter({
                    aoColumns: [
                        null,
                        { sSelector: "#filter_tlist_order_entry_id", type: "text"},
                        null,
                        null,
                        null
                        ]
                });
            });
        </script>
        </div>
    </div>
    <!-- main content ends -->
</div>
<!-- content ends -->
@endsection
