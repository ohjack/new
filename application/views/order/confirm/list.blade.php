@layout('layout')
@section('script')
{{ HTML::script('js/order.js') }}
@endsection
@section('content')
    <div>
        <a href="/order/center" class="f_r m_r_15">返回</a>
    </div>
    {{ Form::open('order/doconfirm') }}
    <table class="table">
      <thead>
        <tr>
            <th width="40px"><label>{{ Form::checkbox('select_all', '') }}全选</label></th>
            <th width="80px">标识</th>
            <th width="130px">订单ID</th>
            <th width="120px">购买时间</th>
            <th>订单总金额</th>
            <th>快递级别</th>
            <th>SKUs</th>
            <th>状态</th>
            <th>来源</th>
        </tr>
      </thead>
      <tbody>
        @foreach($orders->results as $order)
        <tr class="order" title="双击查看订单详情" key="{{ $order->id }}">
          <td>
              {{ Form::checkbox('id[]', $order->id, '', ['style' => 'margin-left: 10px;']) }}
          </td>
          <td>
              @foreach($order->marks as $mark)
                  {{ HTML::link('order?mark_id=' . $mark->id, $mark->name, 'style="color:' . $mark->color . '"') }}
              @endforeach
          </td>
          <td>
                {{$order->entry_id}}<br />
          </td>
          <td>{{$order->created_at}}</td>
          <td>{{$order->currency}} {{$order->total}}</td>
          <td @if($order->shipment_level == 'Expedited') style="color:red" @endif>
            {{$order->shipment_level}}
          </td>
          <td>
            @foreach($order->items as $item)
                {{ $item->sku }} x {{ $item->quantity}}<br />
            @endforeach
          </td>
          <td>{{$order->from}}</td>
          <td>{{ Config::get('application.order_status')[$order->order_status] }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="9">
                {{ Form::submit('确认') }}
          </td>  
        </tr>
        <tr>
          <td colspan="9">
            {{ $orders->links() }} {{ $orders->page * $orders->per_page - $orders->per_page + 1 }}-{{$orders->page * $orders->per_page }} of {{ $orders->total }} {{ $orders->per_page }} per page
          </td>
        </tr>
      </tfoot>
    </table>
    {{ Form::close() }}
    <div class="order_detail">
        <div class="title"><em>X</em>订单详情：</div>
        <div style="overflow: auto;zoom:1">
            <div class="tab">
                <ul>
                    <li panel="order_info" class="tab_current">订单详情</li>
                    <li panel="items_list">产品列表</li>
                </ul>
            </div>
            <div class="mark">
                <span>标识:</span>
            </div>
        </div>
        <div class="detail">
            <table style="width: 100%" id="order_info">
                <tbody>
                  <tr>
                    <th width="80em">订单ID：</th>
                    <td field='entry_id'></td>
                  </tr>
                  <tr>
                    <th width="80em">来源：</th>
                    <td field='from'></td>
                  </tr>
                  <tr>
                    <th width="80em">买家姓名：</th>
                    <td field='name'></td>
                  </tr>
                  <tr>
                    <th width="80em">购买时间：</th>
                    <td field='created_at'></td>
                  </tr>
                  <tr>
                    <th width="80em">买家Email：</th>
                    <td field='email'></td>
                  </tr>
                  <tr>
                    <th width="80em">收货人：</th>
                    <td field='shipping_name'></td>
                  </tr>
                  <tr>
                    <th width="80em">收货地址：</th>
                    <td field='shipping_address'></td>
                  </tr>
                  <tr>
                    <th width="80em">收货城市：</th>
                    <td field='shipping_city'></td>
                  </tr>
                  <tr>
                    <th width="80em">收货州/省：</th>
                    <td field='shipping_state_or_region'></td>
                  </tr>
                  <tr>
                    <th width="80em">收货国家：</th>
                    <td field='shipping_country'></td>
                  </tr>
                  <tr>
                    <th width="80em">邮编：</th>
                    <td field="shipping_postal_code"></td>
                  </tr>
                  <tr>
                    <th width="80em">电话：</th>
                    <td field='shipping_phone'></td>
                  </tr>
                  <tr>
                    <th width="80em">快递级别：</th>
                    <td field='shipment_level'></td>
                  <tr>
                    <th width="80em">订单总额：</th>
                    <td field='total'></td>
                  </tr>
                  <tr>
                    <th width="80em">订单状态：</th>
                    <td field='status'></td>
                  </tr>
                  <tr>
                    <th width="80em">处理   状态：</th>
                    <td field='order_status'></td>
                  </tr>
                </tbody>
            </table>
            <table style="width: 100%; display:none" id="items_list">
              <thead>
                <tr>
                  <th width="5%">ID</th>
                  <th>名称</th>
                  <th width="15%">SKU</th>
                  <th width="10%">单价</th>
                  <th width="5%">数目</th>
                  <th width="10%">邮费</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
        </div>
    </div>
@endsection
