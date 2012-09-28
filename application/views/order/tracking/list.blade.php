@layout('layout')
@section('script')
{{ HTML::script('js/order.js') }}
{{ HTML::script('js/ajaxfileupload.js') }}
@endsection
@section('content')
    <div style="float: right; margin-right: 20px;">{{ HTML::link('order/center', '返回') }}</div>
    <div style="clear: both"></div>
    <div style="margin: 0 15px">
        {{ Form::open('order/tracking', 'GET') }}
        <span style="float: right;">订单ID：<input name="entry_id" value=""> <input type="submit" value="搜索"></span>
        {{ Form::close() }}
        <form name="form" action="" method="POST" enctype="multipart/form-data">
            导入文件<input id="import_file" name="import_file" type="file"><input type="button" value="上传" id="import_logistic"><span style="display: none" id="upload_tips"></span><a href="#">下载导入模板</a>
        </form>
    </div>
    {{ Form::open('shipping') }}
    <table class="table">
      <thead>
        <tr>
            <th width="130px">订单ID</th>
            <th width="120px">物流公司</th>
            <th>物流方式</th>
            <th>跟踪号</th>
            <th>是否提前发货</th>
        </tr>
      </thead>
      <tbody>
        <script>
            var logistic_company = {{ json_encode($logistic_company) }};
        </script>
        @foreach($orders->results as $order)
        <tr class="order_logistic" title="双击展开产品">
          <td>{{$order->entry_id}}</td>
          <td>
              <select name="logistic[{{ $order->id }}][compay]" class="logistic_company">
                   <option value>--请选择--</option>
                   @foreach($logistic_company as $key => $company)
                   <option value="{{ $key }}">{{ $key }}</option>
                   @endforeach
              </select>
          </td>
          <td>
              <select name="logistic[{{ $order->id }}][method]" class="logistic_method">
                  <option value>--请选择--</option>
              </select>
          </td>
          <td>{{ Form::text('logistic[' . $order->id . '][tracking_no]') }}</td>
          <td>{{ Form::checkbox('logistic[' . $order->id . '][ship_first]') }}</td>
        </tr>
        <tr style="display:none">
            <td colspan="5" style="background: #fee">
                <table style="margin: 0;" class="item_logistic">
                    <thead>
                        <tr>
                            <th>产品ID</th>
                            <th>SKU</th>
                            <th>发货数量</th>
                            <th>物流公司</th>
                            <th>物流方式</th>
                            <th>跟踪号</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                          <td>{{ $item->entry_id }}</td>
                          <td>{{ $item->sku }}</td>
                          <td>{{ Form::text('logistic[' . $order->id .'][items][' . $item->id . '][ship_quantity]', $item->quantity, ['style'=>'width:30px']) }}/{{ $item->quantity }}</td>
                          <td>
                              <select name="logistic[{{ $order->id }}][items][{{ $item->id }}][compay]" class="logistic_company">
                                   <option value>--请选择--</option>
                                   @foreach($logistic_company as $key => $company)
                                   <option value="{{ $key }}">{{ $key }}</option>
                                   @endforeach
                              </select>
                          </td>
                          <td>
                              <select name="logistic[{{ $order->id }}][items][{{ $item->id }}][method]" class="logistic_method">
                                  <option value>--请选择--</option>
                              </select>
                          </td>
                          <td>{{ Form::text('logistic[' . $order->id .'][items][' . $item->id . '][tracking_no]') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5">
            {{ $orders->links() }} {{ $orders->page * $orders->per_page - $orders->per_page + 1 }}-{{$orders->page * $orders->per_page }} of {{ $orders->total }} {{ $orders->per_page }} per page
          </td>
        </tr>
      </tfoot>
    </table>
    {{ Form::close() }}
@endsection
