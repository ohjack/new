@layout('layout')
@section('script')
@endsection
@section('content')
    <div style="float: right; margin-right: 20px">{{ HTML::link('order/center', '返回') }}</div>
    <div style="clear: both"></div>
    <ul>
      @foreach($files as $file)
      <li><a href="{{ DS . 'data' . DS . 'logistics_file' . DS . $file['filename'] }}">{{ $file['name'] }}系统物流订单数据[右键下载]</a><span>(共{{ $file['total'] }}条记录)</li>
      @endforeach
    </ul>
@endsection
