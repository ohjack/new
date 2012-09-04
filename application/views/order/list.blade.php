<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>外贸电商交换机系统</title>
    <meta name="viewport" content="width=device-width">
    {{ HTML::style('laravel/css/style.css') }}
    {{ HTML::script('js/jquery.js') }}
    {{ HTML::script('laravel/js/order.js') }}
</head>
<body>
    <div class="nav">抓取订单->匹配物流->导出</div>
    <div style="">{{ HTML::link('test', '抓取订单') }} {{ HTML::link('match', '重新匹配') }}</div>
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
            <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($orders->results as $order)
        <tr class="order">
          <td>
                <input type="checkbox" name="id[]" value="{{$order->id}}">
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
          <td>{{$order->status}}</td>
          <td class="action"><a href="#">handle</a></td>
        </tr>
        <tr style="display:none" id="items{{$order->id}}">
            <td colspan="10">
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
          <td colspan="10">
            <label><input type="checkbox" name="selectAll"> 全选</label>
            <select name="action">
                <option>-请选择-</option>
                <option>不处理</option>
            </select>
            <input type="button" value="提交" /> 
            {{$orders->links()}}
          </td>
        </tr>
      </tfoot>
    </table>
    <div id="addSkuMap">
        <table style="width:100%">
          <thead>
            <tr>
              <th colspan="2">添加SKU映射<span id="close">X</span></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>原SKU:</td>
              <td><input type="text" name="original_sku"></td>
            </tr>
            <tr>
              <td>目标SKU:</td>
              <td><input type="text" name="target_sku"></td>
            </tr>
            <tr>
              <td>物流系统:</td>
              <td>
                <select name="logistics">
                  <option value="coolsystem">酷系统</option>
                  <option value="birdsystem">鸟系统</option>
                </select>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2"><input id="skuMapSubmit" type="button" value="提交" /><span id="skuTips"></span></td>
            </tr>
          </tfoot>
        </table>
    </div>
</body>
</html>
