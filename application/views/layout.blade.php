<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>{{ $title }} - 外贸电商交换机系统</title>
    <meta name="viewport" content="width=device-width">
    {{ HTML::style('css/style.css') }}
    {{ HTML::script('js/jquery.js') }}
    {{ HTML::script('js/common.js') }}
    @yield('script')
</head>
@section('dashboard')
  <div>
       {{ HTML::link('order', '订单列表') }}
       {{ HTML::link('order/handle', '处理订单') }}
       {{ HTML::link('skumap/manage', '产品设置管理') }}
       <!--{{ HTML::link('#','平台设置') }}
       {{ HTML::link('#','系统概况') }}
       {{ HTML::link('#','销售数据') }} -->
  </div>
@endsection
<body>
    <div>
        <div class="header">
            <div class="dashboard">@yield('dashboard')</div>
        </div>
        <div class="content">@yield('content')</div>
        <div class="footer">Copyright (c) 2012 EIMO Technology Company All Rights Reserved.</div>
    </div>
    <div class="loading"><img src="/img/loading.gif"> 数据加载中...</div>
    <div class="mask"></div>
</body>
</html>
