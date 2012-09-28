@layout('layout')
@section('script')
{{ HTML::script('js/order.js') }}
@endsection  
@section('content')
<div id="step">
    <div class="content">
        <a href="javascript:;"  id="spiderOrders"><div class="box">抓单<em class="circle">{{ $total['order'] }}</em></div></a>
        <a href="/order/skumap"><div class="box">分析@if($total['skumap'])<em class="circle">{{ $total['skumap']}}</em>@endif</div></a>
        <a href="/order/handle"><div class="box">处理</div></a>
        <a href="/order/tracking"><div class="box">跟踪</div></a>
        <a href="/order/confirm"><div class="box">确认</div></a>
        <a href="/order?"><div class="box">完成</div></a>
    </div>
</div>
@endsection  
