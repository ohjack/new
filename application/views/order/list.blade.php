@layout('layout')
@section('script')
{{ HTML::script('js/order.js') }}
@endsection
@section('content')
    <table class="table">
      <thead>
        <tr>
            <th>Order Id</th>
            <th>Purchase Date</th>
            <th>Shipping Address</th>
            <th>Total</th>
            <th>Shipment Level</th>
            <th>Ship Level</th>
            <th>Payment Method</th>
            <th>Form</th>
            <th>Status</th>
            <!--th>Action</th-->
        </tr>
      </thead>
      <tbody>
        @foreach($orders->results as $order)
        <tr class="order">
          <td>
                <!--input type="checkbox" name="id[]" value="{{$order->id}}"-->
                #{{$order->id}}<br />
          </td>
          <td>{{$order->created_at}}</td>
          <td>
            {{$order->shipping_name}}<br />
            {{$order->shipping_address3}} {{$order->shipping_address2}} {{$order->shipping_address1}}<br />
            {{$order->shipping_city}} {{$order->shipping_state_or_region}} {{$order->shipping_country}}<br />
            {{$order->shipping_postal_code}}<br />
            Tel:{{$order->shipping_phone}}<br />
          </td>
          <td>{{$order->currency}} {{$order->total}}</td>
          <td @if($order->shipment_level == 'Expedited') style="color:red" @endif>
            {{$order->shipment_level}}
            <div class="openItem" key="{{$order->id}}">
                <span class="open"></span>
            </div>
          </td>
          <td>{{$order->ship_level}}</td>
          <td>{{$order->payment_method}}</td>
          <td>{{$order->from}}</td>
          <td>{{$order->order_status}}</td>
          <!--td class="action"><a href="#">handle</a></td-->
        </tr>
        <tr style="display:none" id="items{{$order->id}}">
            <td colspan="9">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Item Id</th>
                      <th>Name</th>
                      <th>SKU</th>
                      <th>Price</th>
                      <th>Quantity</th>
                      <th>Shipping Cose</th>
                    </tr>
                  </thead>
                  <tbody id="show_items_{{$order->id}}">
                  </tbody>
                </table>
            </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="9">
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
@endsection
