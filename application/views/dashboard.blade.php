@layout('layout')
@section('script')
{{ HTML::script('js/files/common.js') }}
{{ HTML::script('js/files/dashboard.js') }}
{{ HTML::script('js/files/order.js') }}
@endsection
@section('sidebar')
    @include('sidebar')
@endsection
@section('content')
<!-- Content begins -->
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-screen"></span>仪表盘</span>
        <ul class="quickStats">
            <li>
                <a href="" class="blueImg"><img src="images/icons/quickstats/plus.png" alt="" /></a>
                <div class="floatR"><strong class="blue">5489</strong><span>visits</span></div>
            </li>
            <li>
                <a href="" class="redImg"><img src="images/icons/quickstats/user.png" alt="" /></a>
                <div class="floatR"><strong class="blue">4658</strong><span>users</span></div>
            </li>
            <li>
                <a href="" class="greenImg"><img src="images/icons/quickstats/money.png" alt="" /></a>
                <div class="floatR"><strong class="blue">1289</strong><span>orders</span></div>
            </li>
        </ul>
        <div class="clear"></div>
    </div>
    
    <!-- Breadcrumbs line -->
    <div class="breadLine">
        <div class="bc">
            <ul id="breadcrumbs" class="breadcrumbs">
                <li><a href="{{ URL::base() }}">仪表盘</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main content -->
    <div class="wrapper">
        <ul class="middleNavR">
            <li><a href="javascript:;" title="抓取订单" class="tipN"><span class="iconb step" data-icon=""></span></a></li>
            <li><a href="javascript:;" title="分析订单" class="tipN"><span class="iconb step" data-icon=""></span></a><strong>8</strong></li>
            <li><a href="javascript:;" title="处理订单" class="tipN"><span class="iconb step" data-icon=""></span></a></li>
            <li><a href="javascript:;" title="跟踪订单" class="tipN"><span class="iconb step" data-icon=""></span></a></li>
            <li><a href="javascript:;" title="完成订单" class="tipN"><span class="iconb step" data-icon=""></span></a></li>
        </ul>

        <!--orders begins-->
        <div class="widget">
            <div class="whead"><span class="titleIcon check"><input type="checkbox" id="titleCheck" name="titleCheck" /></span><h6>订单列表</h6><div class="clear"></div></div>
            <div id="order_list" class="hiddenpars">
                <a href="javascript:;" id="order_list_fullscreen" class="tOptions1 tipS" title="全屏"><img src="{{URL::base()}}/images/icons/fullscreen" alt=""/></a>
                <a href="javascript:;" id="order_list_search" class="tOptions2 tipS" title="搜索"><img src="{{URL::base()}}/images/icons/search" alt=""/></a>
                <a href="javascript:;" id="order_list_options" class="tOptions3 tipS" title="设置"><img src="{{URL::base()}}/images/icons/options" alt=""/></a>
                <table cellpadding="0" cellspacing="0" border="0" class="dTable" id="order_list_table">
                    <thead>
                        <tr>
                            <td><img src="images/elements/other/tableArrows.png" alt="" /></td>
                            <td>标识</td>
                            <td>订单ID</td>
                            <td>SKUs</td>
                            <td>状态</td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                      </tr>
                    </tfoot>
                </table>
                </div>
            <div class="clear"></div> 
        </div>
        <!--orders ends-->
        <!--orders table script begins-->
        <script type="text/javascript">
            $(function(){
                // 表格初始化
                oTable = $('#order_list_table').dataTable({
                    "bSort": false,
                    "bProcessing" : true,
                    "bFilter": true,
                    "bServerSide": true,
                    "bJQueryUI": false,
                    "bAutoWidth": false,
                    "sPaginationType": "full_numbers",
                    "sAjaxSource": "/order/ajax/list",
                    "sDom": '<"H"<"#olist_options"<"#olist_length"l>><"#olist_search"<"#price_fitter">>>tr<"F"ip>'
                }).columnFilter({aoColumns:[
                            null,
                            null,//{ sSelector: "#order_id_fitter", type:"text"}
                            null,
                            null,
                            { sSelector: "#price_fitter", type:"text"}
                            ]
                });
            }); 
        </script>
        <!--orders table script ends-->
    </div>
    <!-- Main content ends -->
    
</div>
<!-- Content ends -->
@endsection

