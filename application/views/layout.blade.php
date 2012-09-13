<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>外贸电商交换机系统</title>
    <meta name="viewport" content="width=device-width">
    {{ HTML::style('css/style.css') }}
    {{ HTML::script('js/jquery.js') }}
    {{ HTML::script('js/common.js') }}
    @yield('script')
</head>
@section('dashboard')
  <div>
       {{ HTML::link('order', '订单列表') }}
  </div>
  <div>
  @foreach(Config::get('application.steps') as $step)
    <a href="{{ $step['link'] }}" id="{{ $step['id'] }}" class="{{ $step['class'] }}">{{ $step['name'] }}</a>
  @endforeach
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
