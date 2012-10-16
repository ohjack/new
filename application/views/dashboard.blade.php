@layout('layout')
@section('script')
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
        <span class="pageTitle"><span class="icon-screen"></span>控制中心</span>
        @render('tinfo')
    </div>
    
    <!-- Breadcrumbs line -->
    <div class="breadLine">
        <div class="bc">
            <ul id="breadcrumbs" class="breadcrumbs">
                <li><a href="{{ URL::base() }}">控制中心</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main content -->
    <div class="wrapper">
        <ul class="middleNavR">
            <li><a id="spider" href="javascript:;" title="抓取订单" class="tipN"><span class="iconb step" data-icon=""></span></a>@if($total['order'])<strong>{{ $total['order'] }}</strong>@endif</li>
            <li><a href="{{ URL::base() }}/skumap" title="分析订单" class="tipN"><span class="iconb step" data-icon=""></span></a>@if($total['skumap'])<strong>{{ $total['skumap']}}</strong>@endif</li>
            <li><a href="{{ URL::base() }}/order/handle" title="处理订单" class="tipN"><span class="iconb step" data-icon=""></span></a>@if($total['handle'])<strong>{{ $total['handle']}}</strong>@endif</li>
            <li><a href="{{ URL::base() }}/order/tracking" title="跟踪订单" class="tipN"><span class="iconb step" data-icon=""></span></a>@if($total['logistics'])<strong>{{ $total['logistics']}}</strong>@endif</li>
            <li><a href="javascript:;" title="完成订单" class="tipN"><span class="iconb step" data-icon=""></span></a></li>
        </ul>

        <!-- tips begins-->
        <div id="spider_tips" title="抓取提示" style="display:none">
            <div id="spider_orders"><span style="line-height: 16px"><img src="{{ URL::base() }}/images/elements/loaders/10s.gif" style="float: left;" alt="">抓取订单中...</span></div>
            <div id="spider_orders_info"></div>
            <div id="spider_items"><span style="line-height: 16px"><img src="{{ URL::base() }}/images/elements/loaders/10s.gif" style="float: left;" alt="">抓取产品中...</span></div>
            <div id="spider_items_info"></div>
            <div id="spider_success" style="display: none">抓取完成，请关闭</div>
        </div>
        <!-- tips ends -->

        <!--orders begins-->
        <div class="widget fluid" id="olist">
            <div class="whead"><h6>订单列表</h6><div class="clear"></div></div>
            <div id="order_list" class="hiddenpars">
                <div class="cOptions">
                    <a href="javascript:;" class="tOptions tipS doFullscreen" key="olist" title="全屏"><img src="{{URL::base()}}/images/icons/fullscreen" alt=""/></a>
                    <a href="javascript:;" ckey="olist_search" class="tOptions tipS" title="搜索"><img src="{{URL::base()}}/images/icons/search" alt=""/></a>
                    <a href="javascript:;" ckey="olist_options" class="tOptions tipS" title="设置"><img src="{{URL::base()}}/images/icons/options" alt=""/></a>
                </div>
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
                            <th key="{{$key}}">{{ $field['name'] }}</th>
                            @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot style="display: none;"><!--悲催的colnums filter插件用-->
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
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
                colnums = new Array;
                // 表格初始化
                oTable = $('#order_list_table').dataTable({
                    "bSort": false,
                    "bProcessing" : true,
                    "bFilter": true,
                    "bServerSide": true,
                    "bJQueryUI": false,
                    "bAutoWidth": true,
                    "sPaginationType": "full_numbers",
                    "sAjaxSource": "/order/ajax/list",
                    "sDom": '<"H"<"#olist_options"<"#olist_length"l><"clear"><"divider"><"#olist_fields">><"#olist_search"<"#filter_olist_order_entry_id"><"#filter_olist_order_status"><"#filter_olist_from">>>tr<"F"ip>',
                    "fnDrawCallback": function() {
                        reset_list();
                    },
                    "fnInitComplete": function() {
                        init();
                    },
                    aoColumns: [
                        { "fnRender": function( oObj ){
                            return '<input type="checkbox" class="check" name="ids[]" value ="' + oObj.aData[0] + '">';
                        } },null,null,null,null,null,null,null,null,null,null,null,null,null
                    ]
                }).columnFilter({
                    aoColumns:[
                        null,
                        { sSelector: "#filter_olist_order_entry_id",type:"text"  },
                        null,
                        null,
                        null,
                        { sSelector: "#filter_olist_order_status", type:"select" },
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        { sSelector: "#filter_olist_from",type:"select", values:['Amazon.com', 'Amazon.co.uk'] }
                        ]
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

            // 初始化
            function init() {
                init_options();
                init_search();
            }

            // 隐藏显示列
            function reset_list() {
                $('#order_list_table').find('tr').each(function(){
                    $(this).children('th, td').not(':first').hide();
                }); 
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
                $("#order_list_table .check").not('#titleCheck').uniform();
            }

            // 格式化搜索
            function init_search() {
                $('#olist_search div').each(function(){
                    var filter = $(this);
                    var key = filter.attr('id').replace('filter_olist_', '');
                    $('#order_list_table').find('th').each(function() {
                        if($(this).attr('key') == key) {
                            var filter_name = $(this).html();
                            filter.find('span').addClass('grid3');
                            filter.prepend('<div class="grid2" style="line-height: 26px">' + filter_name + ':</div>');
                            filter.append('<div class="clear"></div>');
                            filter.parent().addClass('formRow').css('border', '0');
                        }
                    });
                });

                // 订单状态
                <?php $options = '<option value="">--请选择--</option>'; ?>
                @foreach(Config::get('application.order_status') as $key => $status)
                    <?php $options .= '<option value=' . $key . '>' . $status['desc'] . '</option>'; ?>
                @endforeach
                var options = '<?php echo $options;?>';

                $('#filter_olist_order_status select').html(options);

                $("#olist_search select").uniform();
                $("#olist_search select").prev().html('--请选择--');
                $("#olist_search option.search_init").html('--请选择--');
            }
        </script>
        <!--orders table script ends-->
    </div>
    <!-- Main content ends -->
    
</div>
<!-- Content ends -->
@endsection

