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
            <div class="whead"><h6>订单列表</h6><div class="clear"></div></div>
            <div id="order_list" class="hiddenpars">
                <a href="javascript:;" id="order_list_fullscreen" class="tOptions1 tipS" title="全屏"><img src="{{URL::base()}}/images/icons/fullscreen" alt=""/></a>
                <a href="javascript:;" id="order_list_search" class="tOptions2 tipS" title="搜索"><img src="{{URL::base()}}/images/icons/search" alt=""/></a>
                <a href="javascript:;" id="order_list_options" class="tOptions3 tipS" title="设置"><img src="{{URL::base()}}/images/icons/options" alt=""/></a>
                <div id="olist_fieds_hide" style="display:none">
                    <h6 class="mb20">列设置</h6>
                    <div class="fields">
                        @foreach(Config::get('order_list_fields') as $key => $field)
                        @if($field['name'])
                        <dd style="width: 20%" class="floatL">
                            <input type="checkbox" id="{{$key}}" @if(in_array($key, $order_list_columns))checked="checked"@endif>
                            <label for="{{$key}}" class="mr20">{{$field['name']}}</label>
                        </dd>
                        @endif
                        @endforeach
                        <span class="clear"></span>
                    </div>
                </div>
                <table cellpadding="0" cellspacing="0" border="0" class="dTable" id="order_list_table">
                    <thead>
                        <tr>
                            @foreach(Config::get('order_list_fields') as $key => $field)
                            @if($key == 'order_id')
                            <th>
                                <input class="check" type="checkbox" id="titleCheck" name="titleCheck" />
                                <label for="titleCheck">全选</label>
                            </th>
                            @else
                            <th>{{ $field['name'] }}</th>
                            @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
            <div class="clear"></div> 
        </div>
        <!--orders ends-->
        <!--orders table script begins-->
        <script type="text/javascript">
            $(function(){
                colnums = new Array;
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
                    "sDom": '<"H"<"#olist_options"<"#olist_length"l><"clear"><"divider"><"#olist_fields">><"#olist_search"<"#price_fitter">>>tr<"F"ip>',
                    "fnDrawCallback": function() {
                        reset_order_list();
                    }
                });/*.columnFilter({aoColumns:[
                            null,
                            null,//{ sSelector: "#order_id_fitter", type:"text"}
                            null,
                            null,
                            { sSelector: "#price_fitter", type:"text"}
                            ]
            });*/
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

                // 加载用户配置
                <?php $i = 0;?>
                @foreach($order_list_columns as $order_list_column)
                    @if(isset(Config::get('order_list_fields')[$order_list_column]['name']))
                    colnums[{{$i}}] = '{{ Config::get('order_list_fields')[$order_list_column]['name'] }}';
                    <?php $i++; ?>
                    @endif
                @endforeach
            }); 

            // 隐藏显示列
            function reset_order_list(){
                $('#order_list_table').find('th, td').hide(); 
                $('#order_list_table').find('th').each(function(){
                    var colname =  $(this).text();
                    for ( var index in colnums) {
                        if(colnums[index] == colname) {
                            $(this).show();
                            var index = $(this).index();
                            $('#order_list_table').find('tr').each(function(){
                                $(this).children('td').eq(index).show();
                            });
                            break;
                        }
                    }
                });
            }
        </script>
        <!--orders table script ends-->
    </div>
    <!-- Main content ends -->
    
</div>
<!-- Content ends -->
@endsection

