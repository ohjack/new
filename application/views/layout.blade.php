<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>外贸电商交换机系统</title>
    <meta name="viewport" content="width=device-width">
    {{ HTML::style('css/style.css') }}
    {{ HTML::script('js/jquery.js') }}
    @yield('script')
    {{ HTML::script('js/common.js') }}
</head>
@section('dashboard')
  <div>
       {{ HTML::link('order', '订单列表') }}
  </div>
  <div>
  <!--{{ HTML::link('order', '订单列表') }}-->
  <span class="click" id="getOrders">抓取订单</span>
  {{ HTML::link('skumap', '产品设置') }}
  <span class="click" id="logistics">匹配物流</span>
  {{ HTML::link('order/logistics', '物流处理') }}
  <span id="tips"></span>
  </div>
@endsection
<body>
    <div>
        <div class="header">
            <div class="dashboard">@yield('dashboard')</div>
        </div>
        <div class="content">@yield('content')</div>
        <div class="footer"></div>
    </div>
</body>
</html>
