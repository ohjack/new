@layout('layout')
@section('script')
@endsection
@section('content')
    <div style="float: right; margin-right: 20px">{{ HTML::link('order/center', '返回') }}</div>
    <div style="clear: both"></div>
    
    <div style="margin: 20px; text-align: center">
    <table>
        <thead>
          <tr><td colspan="3" style="font-size: 14px; font-weight: bold"><span>物流信息导出</span></td></tr>
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
    <div style="margin: 20px;text-align: center">
    <table>
      <thead>
        <tr><td colspan="5"><span style="font-size: 14px; font-weight: bold">历史导出记录</span><em style="margin-left: 10px; font-size: 10px">(每个物流只显示最后5个导出历史)</em></td></tr>
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
          <td colspan="5">注：请导出后把物流数据整理后导入到“跟踪”步骤处理</td>
        </tr>
      </tfoot>
    </table>
    </div>
@endsection
