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
        <span class="pageTitle"><span class="icon-battery-3"></span>处理订单</span>
        @render('tinfo')
    </div>
    
    <!-- Breadcrumbs line -->
    <div class="breadLine">
        <div class="bc">
            <ul id="breadcrumbs" class="breadcrumbs">
                <li><a href="{{ URL::base() }}">仪表盘</a></li>
                <li><a href="{{ URL::base() }}/order/handle">处理订单</a></li>
            </ul>
        </div>
    </div>

    <!-- main content-->
    <div class="wrapper">
        <div class="widget fluid">
            <div class="whead"><h6>物流信息导出</h6><div class="clear"></div></div>
            <table class="tDefault formRow" style="width: 100%">
                <thead>
                  <tr>
                    <th>物流系统</th>
                    <th>记录数</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                @forelse($lists as $code => $list)
                <tr>
                  <td>{{ $list['name'] }}</td>
                  <td>{{ $list['total'] }}</td>
                  <td><a href="{{ URL::to('order/export?logistics=' . $code)}}" target="_blank">下载</a></td>
                </tr>
                @empty
                <tr>
                  <td colspan="3" style="text-align: center">没有新记录</td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="widget fluid">
            <div class="whead"><h6>历史导出导出</h6><div class="clear"></div></div>
            <table class="tDefault formRow" style="width: 100%">
              <thead>
                <tr>
                  <th>文件名</th>
                  <th>物流系统</th>
                  <th>记录数</th>
                  <th>导出时间</th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
                @forelse($histories as $code => $history)
                    @foreach($history as $item)
                    <tr>
                        <td key="{{$item->id}}">{{$item->filename}}</td>
                        <td>{{ Config::get('application.logistics')[$code] }}</td>
                        <td>{{$item->total}}</td>
                        <td>{{$item->export_date}}</td>
                        <td><a href="{{URL::to('order/export?filename=')}}{{$item->filename}}">下载</a></td>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center">没有导出历史</td>
                    </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="5" style="padding: 10px; color: #4579AA">注：请导出后把物流数据整理后导入到“跟踪订单”步骤处理</td>
                </tr>
              </tfoot>
            </table>
        </div>
    </div>
    <!-- main content ends -->
</div>
<!-- content ends -->
@endsection
