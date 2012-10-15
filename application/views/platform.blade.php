@layout('layout')
@section('content')
<style>
.dialog {
display: none;
width: 900px;
height: 600px;
position: fixed;
top: 0px;
left: 0PX;
border: 5px solid #CCC;
z-index: 100;
padding: 10px;
background: white;
}
</style>
<script>
$(function(){
	$("#add_platform").click(function(){
		$(".dialog").fadeIn();
	});

});

</script>
<table class='table'>
    <thead>
        <tr></tr>
    </thead>
    <tbody>
        <tr>
            <th>已有平台</th>
            <th>ID</th>
            <th>操作</th>
        <tr>
        @foreach($platforms as $key=>$value)
        <tr>
            <td>{{$value->name}}</td>
            <td>{{$value->id}}</td>
            <td><a href='/user/platformedit/?userplatform_id={{$value->id}}'>编辑</a></td>
        <tr>
        @endforeach
        <tr>
            <th><input type='button' id='add_platform' value='+添加平台'></th>
            <th></th>
            <th></th>
        <tr>

    </tbody>
</table>

<div class='dialog' style="display: none;">
<h1>请选择平台</h1>
@foreach($sysPlatforms as $value)
<li><a href='/user/platformadd/?platform_id={{$value->id}}'>{{$value->name}}</a></li>
@endforeach
</div>


@endsection
