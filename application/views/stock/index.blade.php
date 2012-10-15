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
                <li><a href="{{ URL::base() }}">仪表盘</a></li>
                <li><a href="{{ URL::base() }}/order/stock">库存信息</a></li>
            </ul>
        </div>
    </div>

    <!--div class="wrapper">
        <span class="buttonS bBlue">FBA 库存信息</span>
    </div-->

    
    <div class="wrapper">
        <div class="widget tableTabs"> 
            <div class="whead"><h6>仓储信息</h6><div class="clear"></div></div>
            <ul class="tabs">
                <li><a href="#ttab1">总仓储</a></li>
                <li><a href="#ttab2">FBA</a></li>
            </ul>
            <?php $i = 1;?>
            @foreach($platforms as $platform)
            <div class="tab_container">
                <div id="ttab<?php echo $i++;?>" class="tab_content">
                    <table cellpadding="0" cellspacing="0" width="100%" class="tDefault">
                        <thead>
                          <tr>
                            <th>SKU</th>
                            <th>名称</th>
                            <th>数目</th>
                            <th>状态</th>
                            <th>操作</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach($stocks[$platform] as $stock)
                            <tr>
                              <td>{{ $stock->sku }}</td>
                              <td>&nbsp;</td>
                              <td>{{ $stock->quantity }}</td>
                              <td>@if($stock->status)可销售@else不可销售@endif</td>
                              <td>发货</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
<!-- content ends -->

@endsection
