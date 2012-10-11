@layout('layout')
@section('script')
{{ HTML::script('js/order.js') }}
{{ HTML::script('js/ajaxfileupload.js') }}
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
                <li><a href="{{ URL::base() }}">仪表盘</a></li>
                <li><a href="{{ URL::base() }}/order/tracking">跟踪订单</a></li>
            </ul>
        </div>
    </div>

    <!-- main content-->
    <div class="wrapper">
        <div class="widget fluid">
            <div class="whead"><h6>跟踪信息</h6><div class="clear"></div></div>
            <div class="formRow">
                {{ Form::open('order/tracking', 'GET') }}
                <span style="float: right;">订单ID：<input name="entry_id" value=""> <input type="submit" value="搜索"></span>
                {{ Form::close() }}
                <form name="form" action="" method="POST" enctype="multipart/form-data">
                    导入文件:<input id="import_file" name="import_file" type="file"><input class="buttonS bBlue" type="button" value="上传" id="import_logistic"><span style="display: none" id="upload_tips"></span><a href="{{Route::controller('home')}}/data/demo/shipped.xls">下载导入模板</a>
                </form>
            </div>
            {{ Form::open('shipping') }}
            <table class="tDefault formRow" style="width: 100%" id="tracking_table">
              <thead>
                <tr>
                    <th width="150px">订单ID</th>
                    <th width="120px">物流公司</th>
                    <th>物流方式</th>
                    <th>跟踪号</th>
                    <th>是否提前发货</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="5">
                  </td>
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
                    "sDom": '<"H"<"#tlist_options"<"#ttist_length"l><"clear"><"divider"><"#tlist_fields">><"#tlist_search"<"#filter_tlist_order_entry_id"><"#filter_tlist_order_status"><"#filter_tlist_from">>>tr<"F"ip>'
                });
            });

        </script>
    </div>
    <!-- main content ends -->
</div>
<!-- content ends -->
@endsection
