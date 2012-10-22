@layout('layout')
@section('script')
@endsection
@section('sidebar')
    @include('sidebar')
@endsection
@section('content')
<!-- content begins -->
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-target-2"></span>库存信息</span>
        @render('tinfo')
    </div>
    
    <!-- Breadcrumbs line -->
    <div class="breadLine">
        <div class="bc">
            <ul id="breadcrumbs" class="breadcrumbs">
                <li><a href="{{ URL::base() }}">控制中心</a></li>
                <li><a href="{{ URL::base() }}/order/stock">库存信息</a></li>
            </ul>
        </div>
    </div>

    <div class="wrapper">
        <div class="widget tableTabs fluid"> 
            <div class="whead"><h6>仓储信息</h6><div class="clear"></div></div>
            <ul class="tabs">
                <?php $i=1; ?>
                @foreach($platforms as $platform)
                <li><a href="#ttab<?php echo $i++; ?>">{{ $platform->name }}</a></li>
                @endforeach
            </ul>
            <?php $i = 1;?>
            @foreach($platforms as $platform)
            <div class="tab_container">
                <div id="ttab<?php echo $i; ?>" class="tab_content">
                    <table cellpadding="0" cellspacing="0" width="100%" class="dTable" id="ttable<?php echo $i; ?>">
                        <thead>
                          <tr>
                            <th width="100px" key="sku">SKU</th>
                            <th>名称</th>
                            <th width="40px">数量</th>
                            <th width="60px">状态</th>
                            <th width="40px">操作</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot style="display:none">
                          <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <script type="text/javascript">
                $(function(){
                    tTable<?php echo $i; ?> =  $('#ttable<?php echo $i;?>').dataTable({
                        "bSort": false,
                        "bProcessing" : true,
                        "bFilter": true,
                        "bServerSide": true,
                        "bJQueryUI": false,
                        "bAutoWidth": true,
                        "sPaginationType": "full_numbers",
                        "sAjaxSource": "{{ URL::base() }}/stock/list?platform_id={{$platform->id}}",
                        "sDom": '<"H"<"#slist_search<?php echo $i;?>"<"#filter_slist<?php echo $i; ?>_sku">>l>tr<"F"ip>',
                        aoColumns: [
                            null,
                            { bSearchable : false },
                            { bSearchable : false },
                            { 
                                bSearchable : false,
                                fnRender : function( oObj ) { 
                                    if(oObj.aData[3] == 1) 
                                        return '可发货'; 
                                    else 
                                        return '不可发货';
                                } 
                            },
                            { 
                                bSearchable : false,
                                fnRender : function( oObj ) { 
                                    var style = 'inputShipInfo';
                                    if(oObj.aData[3] != '可发货' || oObj.aData[2] < 1) {
                                        style = 'disabled';
                                    }

                                    return '<a href="javascript:;" sku="' + oObj.aData[0] + '" cid="'+ oObj.aData[4] +'" class="' + style + ' tablectrl_small bDefault tipS" title="发货"><span class="iconb" data-icon=""></span></a>'; 
                                }
                            }
                        ],
                        fnInitComplete: function() {
                            $('.tablePars').css('height', '61px');
                            $('.tipS').tipsy({gravity: 's',fade: true, html:true});
                            init_search<?php echo $i;?>();
                        },
                    }).columnFilter({
                        aoColumns:[
                        { sSelector: "#filter_slist<?php echo $i;?>_sku", type: "text" },
                        null, null, null, null
                    ]});

                    // Dialog
                    $('#shipInfo').dialog({
                        autoOpen: false,
                        modal: true,
                        closeOnEscape: false,
                        draggable: false,
                        resizable: false,
                        width: 700,
                        buttons: {
                            "取消": function () {
                              $(this).dialog("close");  
                            },
                            "发货": function () {
                                alert('发货中...');
                            }
                        }
                    });

                    $('.inputShipInfo').live('click',function(){
                        var sku = $(this).attr('sku');
                        var id  = $(this).attr('cid');
                        $('#ui-dialog-title-shipInfo').html('发货信息: ' + sku);
                        $('#shipInfoForm input[name="sku"]').val(sku);
                        $('#shipInfoForm input[name="id"]').val(id);
                        $('#shipInfo').dialog('open');
                        return false;
                    });
                });

                // 格式化搜索
                function init_search<?php echo $i; ?>() {
                    $('#slist_search<?php echo $i; ?> div').each(function(){
                        var filter = $(this);
                        var key = filter.attr('id').replace('filter_slist<?php echo $i; ?>_', '');
                        $('#ttable<?php echo $i;?>').find('th').each(function() {
                            if($(this).attr('key') == key) {
                                filter.addClass('dataTables_filter');
                                var filter_name = $(this).html();
                                filter.find('span').addClass('grid3');
                                filter.prepend('<div class="grid2" style="line-height: 26px">' + filter_name + ':</div>');
                                filter.append('<div class="clear"></div>');
                            }
                        });
                    });
                }
            </script>
            <?php $i++;?>
            @endforeach
        </div>
    </div>
    <!-- Ship info Dialog begins -->
    <div id="shipInfo" class="dialog fluid">
        <form action="" id="shipInfoForm">
            {{ Form::hidden('sku') }}
            {{ Form::hidden('id') }}
            <label class="grid3 textR nomargin">发货数量：</label>
            <input class="grid8 nomargin" type="text" name="quantity" placeholder="发货数量" />
            <span class="clear"></span>
            <div class="divider"></div>
            <label class="grid3 nomargin textR">收货人：</label>
            <input class="grid8 nomargin" type="text" name="shipping_name" placeholder="收货人姓名" />
            <span class="clear"></span>
            <div class="divider"></div>
            <label class="grid3 nomargin textR">电话：</label>
            <input class="grid8 nomargin" type="text" name="shipping_phone" placeholder="收货人电话" />
            <span class="clear"></span>
            <div class="divider"></div>
            <label class="grid3 nomargin textR">收货地址：</label>
            <input class="grid8 nomargin" type="text" name="shipping_address" placeholder="收货人所在地址" />
            <span class="clear"></span>
            <div class="divider"></div>
            <label class="grid3 nomargin textR">收货城市：</label>
            <input class="grid8 nomargin" type="text" name="shipping_city" placeholder="收货人所在城市" />
            <span class="clear"></span>
            <div class="divider"></div>
            <label class="grid3 nomargin textR">收货省/州：</label>
            <input class="grid8 nomargin" type="text" name="shipping_state_or_region" placeholder="收货人所在省/州" />
            <span class="clear"></span>
            <div class="divider"></div>
            <label class="grid3 nomargin textR">收货国家：</label>
            <input class="grid8 nomargin" type="text" name="shipping_country" placeholder="收货人所在国家" />
            <span class="clear"></span>
            <div class="divider"></div>
            <label class="grid3 nomargin textR">邮编：</label>
            <input class="grid8 nomargin" type="text" name="shipping_postal_code" placeholder="收货人邮编" />
        </form>
    </div>
    <!-- Ship info Dialog ends -->

</div>
<!-- content ends -->

@endsection
