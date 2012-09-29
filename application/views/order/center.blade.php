@layout('layout')
@section('script')
{{ HTML::script('js/order.js') }}
@endsection  
@section('content')
<div id="step">
    <div class="content">
        <a href="javascript:;"  id="spiderOrders"><div class="box">抓单@if($total['order'])<em class="circle">{{ $total['order'] }}</em>@endif</div></a>
        <a href="/order/skumap"><div class="box">分析@if($total['skumap'])<em class="circle">{{ $total['skumap']}}</em>@endif</div></a>
        <a href="/order/handle"><div class="box">处理@if($total['handle'])<em class="circle">{{ $total['handle']}}</em>@endif</div></a>
        <a href="/order/tracking"><div class="box">跟踪@if($total['handle'])<em class="circle">{{ $total['handle']}}</em>@endif</div></a>
        <a href="/order/confirm"><div class="box">确认@if($total['confirm'])<em class="circle">{{ $total['confirm']}}</em>@endif</div></a>
        <a href="/order?"><div class="box">完成</div></a>
    </div>
</div>
@endsection  
