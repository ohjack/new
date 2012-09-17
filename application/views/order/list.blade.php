@layout('layout')
@section('script')
{{ HTML::script('js/order.js') }}
@endsection
@section('content')
    <div>
        {{ HTML::link('#', '搜索订单', ['class' => 'f_r m_r_15', 'id' => 'search_order']) }}
        {{ HTML::link('order', '重置搜索', ['class' => 'f_r m_r_15']) }}
        {{ HTML::link('#', '列表设置', ['class' => 'f_r m_r_15', 'id' => 'list_setttings']) }}
    </div>
    <div class="search_order">
        <div class="title"><em>X</em>订单搜索</div>
        {{ Form::open('order', 'GET') }}
            <table>
              <tbody>
                <tr>
                  <th width="80em">订单ID:</th>
                  <td>{{ Form::text('entry_id') }}</td>
                </tr>
                <tr>
                  <th>订单状态:</th>
                  <td>{{ Form::select('order_status') }}</td>
                </tr>
                <tr>
                  <td colspan="2">{{ Form::submit('搜索') }}</td>
                </tr>
              </tbody>
            </table>
        {{ Form::close() }}
    </div>
    <table class="table">
      <thead>
        <tr><td colspan="8">订单列表</td></tr>
        <tr>
            <th>标识</th>
            <th>订单ID</th>
            <th>购买时间</th>
            <!--th>Shipping Address</th-->
            <th>订单总金额</th>
            <th>快递级别</th>
            <th>SKUs</th>
            <!--th>Payment Method</th-->
            <th>来源</th>
            <th>状态</th>
            <!--th>Action</th-->
        </tr>
      </thead>
      <tbody>
        @foreach($orders->results as $order)
        <tr class="order" title="双击查看订单详情" key="{{ $order->id }}">
          <td></td>
          <td>
                <!--input type="checkbox" name="id[]" value="{{$order->id}}"-->
                {{$order->entry_id}}<br />
          </td>
          <td>{{$order->created_at}}</td>
          <!--td>
            {{$order->shipping_name}}<br />
            {{$order->shipping_address3}} {{$order->shipping_address2}} {{$order->shipping_address1}}<br />
            {{$order->shipping_city}} {{$order->shipping_state_or_region}} {{$order->shipping_country}}<br />
            {{$order->shipping_postal_code}}<br />
            Tel:{{$order->shipping_phone}}<br />
          </td-->
          <td>{{$order->currency}} {{$order->total}}</td>
          <td @if($order->shipment_level == 'Expedited') style="color:red" @endif>
            {{$order->shipment_level}}
          </td>
          <td>{{$order->skus}}</td>
          <!--td>{{$order->payment_method}}</td-->
          <td>{{$order->from}}</td>
          <td>{{$order->order_status}}</td>
          <!--td class="action"><a href="#">handle</a></td-->
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="8">
            <!--label><input type="checkbox" name="selectAll"> 全选</label>
            <select name="action">
                <option>-请选择-</option>
                <option>其他物流</option>
            </select>
            <input type="button" value="提交" /-->
            {{ $orders->links() }}
          </td>
        </tr>
      </tfoot>
    </table>
    <div class="order_detail">
        <div class="title"><em>X</em>订单详情：</div>
        <div class="tab">
            <ul>
                <li panel="order_info" class="tab_current">订单详情</li>
                <li panel="items_list">产品列表</li>
            </ul>
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
                    <th width="80em">处理状态：</th>
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
