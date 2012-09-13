@layout('layout')
@section('script')
@endsection
@section('content')
        <ul>
           <li>{{ HTML::link('order/logistics?system=coolsystem', '下载酷系统表单', ['target' => '_blank']) }}({{ $coolsystem_count }})</li>
           <li>{{ HTML::link('order/logistics?system=birdsystem', '下载鸟系统表单', ['target' => '_blank']) }}({{ $birdsystem_count }})</li>
           <li>{{ HTML::link('order/logistics?system=micaosystem', '发送给米巢系统', ['target' => '_blank']) }}({{ $micaosystem_count }})</li>
        </ul>
@endsection
