@layout('layout')
@section('script')
{{ HTML::script('js/order.js') }}
@endsection  
@section('content')
<div style="height: 500px">
<div style="margin: 100px 0 0 100px">
  @foreach(Config::get('application.steps') as $step)
    <div class="step">
        <a href="{{ $step['link'] }}" id="{{ $step['id'] }}" class="{{ $step['class'] }}">{{ $step['name'] }}</a>
    </div>
  @endforeach
  <span id="tips"></span>
  <div style="clear: both"></div>
  </div>
</div>
@endsection  
