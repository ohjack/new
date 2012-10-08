@layout('layout')
@section('script')
{{ HTML::script('js/mark.js')}}
@endsection
@section('content')
{{Form::open('mark/submit','POST')}}
    <table id='mark'  class='table'>
    <tr>
        <th width="5%"><input type="checkbox" id="select_all" name="select_all">全选</th>
        <th width="5%">ID</th>
        <th width="25%">标识</th>
        <th width="10%">字体颜色</th>
        <th width="25%">图标</th>  
        <th width="15%">排序</th>         
    </tr>
    @foreach($marks as $key=>$mark)
    <tr>
        <input type='hidden' name='mark[{{$mark->id}}][mark_id]' value='{{$mark->id}}'>
        <td><input type='checkbox' name='mark[{{$mark->id}}][id]' value='{{$mark->id}}'></td>
        <td>{{$mark->id}}</td>
        <td><input type='text' name='mark[{{$mark->id}}][name]' value='{{$mark->name}}' size='8'></td>
        <td><input type='text' name='mark[{{$mark->id}}][color]' value='{{$mark->color}}' size='8'></td>
        <td><input type='text' name='mark[{{$mark->id}}][ico]' value='{{$mark->ico}}' size='15'></td>
        <td><input type='text' name='mark[{{$mark->id}}][sort]' value='{{$mark->sort}}' size='3'></td>
    </tr>
    @endforeach
    <tr id='tag_add'>
        <td><input type='checkbox' name='add[0][id]'></td>
        <td></td>
        <td><input type='text' name='add[0][name]' size='8'></td>
        <td><input type='text' name='add[0][color]' size='8'>
        </td>
        <td><input type='text' name='add[0][ico]' size='15'></td>
        <td><input type='text' name='add[0][sort]' size='3'></td>
    </tr>
<table>
<input type='button' id='add' value="+"><input type='button' id='subtract' value='-'><input type="submit" value='提交更新'>
<input type='button' id='delete' value='删除所选'>
{{ Form::close() }}
@endsection

