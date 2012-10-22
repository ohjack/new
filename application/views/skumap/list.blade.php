@layout('layout')
@section('script')
{{ HTML::script('js/skumap.js') }}
@endsection
@section('sidebar')
    @include('sidebar')
@endsection
@section('content')
<!-- content begins -->
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-github-4"></span>分析订单</span>
        @render('tinfo')
    </div>
    
    <!-- Breadcrumbs line -->
    <div class="breadLine">
        <div class="bc">
            <ul id="breadcrumbs" class="breadcrumbs">
                <li><a href="{{ URL::base() }}">控制中心</a></li>
                <li><a href="{{ URL::base() }}/skumap">分析订单</a></li>
            </ul>
        </div>
    </div>

    <!-- main content-->
    <div class="wrapper">
<div style="display: none"><a href="{{ URL::to('skumap/manage') }}"  style="float: right; margin-right: 10px">产品设置管理</a></div>
        <div class="widget fluid" id="slist">
            <div class="whead"><h6>产品设置</h6><div class="clear"></div></div>
                <div class="cOptions">
                    <a href="javascript:;" class="tOptions tipS doFullscreen" key="slist" title="全屏"><img src="{{URL::base()}}/images/icons/fullscreen" alt=""/></a>
                </div>
            {{ Form::open('skumap', 'POST') }}
                <table class="table tDefault formRow">
                  <thead>
                    <tr>
                      <th width="110px">SKU</th>
                      <th width="30%">名称</th>
                      <th>国家</th>
                      <th>来源</th>
                      <th>物流</th>
                      <th>品名</th>
                      <th>价值</th>
                      <th>映射SKU</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($items as $item)
                    <tr>
                      <td title="">{{ $item->sku }}</td>
                      <td>{{ $item->name }}</td>
                      <td>{{ $item->shipping_country }}</td>
                      <td>{{ $item->from }}</td>
                      <td>
                          {{ Config::get('application.logistics')[$item->logistics] }}
                          {{ Form::hidden('logistics[]', $item->logistics) }}
                      </td>
                      <td>{{ Form::text('product_name[]') }}</td>
                      <td>{{ Form::text('product_price[]') }}</td>
                      <td>
                          {{ Form::hidden('original_sku[]', $item->sku) }}
                          {{ Form::text('target_sku[]') }}
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="8" class="textR"><input type="submit" value="保存" class="m10 mr10 buttonS bBlue"/></td>
                    </tr>
                  </tfoot>
                </table>
            {{ Form::close() }}
        </div>
    </div>
    <!-- main content ends -->
</div>
<!-- content ends -->
@endsection
