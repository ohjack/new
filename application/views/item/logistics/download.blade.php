@layout('layout')
@section('script')
    {{ HTML::script('laravel/js/download.js') }}
@endsection
@section('content')
        <ul>
           <li>{{ HTML::link('item/logistics?system=coolsystem', '下载酷系统表单', ['target' => '_blank']) }}</li>
           <li>{{ HTML::link('item/logistics?system=birdsystem', '下载鸟系统表单', ['target' => '_blank']) }} </li>
           <li>{{ HTML::link('item/logistics?system=other', '下载其他系统表单', ['target' => '_blank']) }} </li>
        </ul>
@endsection
