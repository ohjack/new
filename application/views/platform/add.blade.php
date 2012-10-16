@layout('layout')
@section('content')
{{ Form::open('user/platformmod','POST')}}
<table>
    <tr>
        <th>平台名称</th>
        <td>{{$platform_name}}</td>
    </tr>
@foreach($option as $key=>$value)
    <tr>
        <th>{{$key}}</th>
        <td><input name='{{$key}}' value='{{$value}}'></td>
    </tr>
@endforeach
<input name='platform_id' type="hidden" value='{{$platform_id}}'>
<input name='userplatform_id' type="hidden" value='{{$userplatform_id}}'>  
    <tr><td><input type="submit" value='确定'></td><td></td></tr>
</table>
{{ Form::close()}}

@endsection