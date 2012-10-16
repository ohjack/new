@layout('layout')
@section('script')
{{ HTML::script('js/files/common.js') }}
{{ HTML::script('js/files/dashboard.js') }}
{{ HTML::script('js/mark.js')}}
@endsection
@section('sidebar')
    @include('sidebar')
@endsection
@section('content')
<!-- Content begins -->
<style>
input[type="text"], input[type="password"]
{
    font-size: 14px;
    border: 1px solid #D7D7D7;
    height: 20px;
}
.cPicker { position: relative; width:150px;}
.cPicker div
{
    position: absolute;
    top: 0px;
    left: 0px;
    width: 24px;
    height: 24px;
    background: url(../images/elements/colorPicker/select.png) center no-repeat;
    cursor: pointer;
}
.dropdown-menu li{
    border-bottom: 1px solid #E4E4E4;
    border-top: 1px solid white;
    display: block;
    padding: 0px 15px;
    clear: both;
    font-weight: normal;
    color: #6A6A6A;
    white-space: nowrap;
    font-size: 11px;
    text-decoration: none;
    height:26px;
}
.dropdown-menu li:hover {
background: #d1cfd0;
}
.btn-group{
}
.btn-group a{
width:55px !important;
}
.btn-group span{

}
</style>
<script type="text/javascript">
$(document).ready(function(){
	$("#mark tbody tr:even").attr('class','even');
	$("#mark tbody td").attr('align','center');
	$("#tag_add").attr('style',"border-bottom: 1px solid #DFDFDF");

	$.configureBoxes();
	$(".select").chosen(); 
	$("select, .check, .check :checkbox, input:radio, input:file").uniform();

	 $(".dropdown-menu li").live("click",function(){
		    	   $(this).parent().prev().find('img').attr({'src':$(this).find('img').attr('src')});
		    	   $(this).parent().prev().find('input').val($(this).find('img').attr('src'));
		    	    
		});

	
	});



</script>
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-screen"></span>仪表盘</span>
        <ul class="quickStats">
            <li>
                <a href="" class="blueImg"><img src="/images/icons/quickstats/plus.png" alt="" /></a>
                <div class="floatR"><strong class="blue">5489</strong><span>visits</span></div>
            </li>
            <li>
                <a href="" class="redImg"><img src="/images/icons/quickstats/user.png" alt="" /></a>
                <div class="floatR"><strong class="blue">4658</strong><span>users</span></div>
            </li>
            <li>
                <a href="" class="greenImg"><img src="/images/icons/quickstats/money.png" alt="" /></a>
                <div class="floatR"><strong class="blue">1289</strong><span>orders</span></div>
            </li>
        </ul>
        <div class="clear"></div>
    </div>
    
    <!-- Breadcrumbs line -->
    <div class="breadLine">
        <div class="bc">
            <ul id="breadcrumbs" class="breadcrumbs">
                <li><a href="{{ URL::base() }}">仪表盘</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main content -->
    {{Form::open('mark/submit','POST')}}
    <div class="wrapper">
        <!--orders begins-->
        <div class="widget">
            <div class="whead"><h6>标识列表</h6><div class="clear"></div></div>
            <div id="order_list" class="hiddenpars">
                <table cellpadding="0" cellspacing="0" border="0" class="dTable dataTable" id="mark" aria-describedby="order_list_table_info">
                    <thead>
                        <tr>
                            <th ><input type="checkbox" id="select_all" name="select_all">全选</th>
                            <th >ID</th>
                            <th >标识</th>
                            <th >字体颜色</th>
                            <th >图标</th>  
                            <th >排序</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marks as $key=>$mark)
                        <tr class='odd'>
                            <input type='hidden' name='mark[{{$mark->id}}][mark_id]' value='{{$mark->id}}'>
                            <td style="display: table-cell;"><input type='checkbox' name='mark[{{$mark->id}}][id]' value='{{$mark->id}}' ></td>
                            <td style="display: table-cell;">{{$mark->id}}</td>
                            <td style="display: table-cell;"><input type='text' name='mark[{{$mark->id}}][name]' value='{{$mark->name}}' size='8'></td>
                            <td style="display: table-cell;"><div class="cPicker"><div style="background-color: {{$mark->color}}"></div><span style="margin-left:-30px;">选择颜色...</span><input type='hidden' name='mark[{{$mark->id}}][color]' value='{{$mark->color}}' size='8'></div></td>
                            <td style="display: table-cell;">
                            <div class="btn-group" style="display: inline-block; margin-bottom: -4px;" >
                                <a class="buttonM bDefault" data-toggle="dropdown" href="#">
                                    <input type='hidden' name='mark[{{$mark->id}}][ico]' value='{{$mark->ico}}' size='15'>
                                    <span><img src="{{$mark->ico}}" /></span><span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" style="width:57px;min-width:57px;">
                                    <li><span><img src="../images/icons/usual/icon-add.png"/></span></li>
                                    <li><span><img src="../images/icons/usual/icon-trash.png" /></span></li>
                                    <li><span><img src="../images/icons/usual/icon-pencil.png" /></span></li>
                                    <li><span><img src="../images/icons/usual/icon-heart.png" /></span></li>
                                </ul>
                            </div>
                            </td>
                            <td style="display: table-cell;"><input type='text' name='mark[{{$mark->id}}][sort]' value='{{$mark->sort}}' size='3'></td>
                        </tr>
                        @endforeach   
                        <tr id='tag_add'>
                            <td style="display: table-cell;"><input type='checkbox' name='add[0][id]'  ></td>
                            <td style="display: table-cell;"></td>
                            <td style="display: table-cell;"><input type='text' name='add[0][name]' size='8'></td>
                            <td style="display: table-cell;"><div class="cPicker"><div style="background-color: #FFF"></div><span style="margin-left:-30px;">选择颜色...</span><input type='hidden' name='add[0][color]' size='8'></div>
                            </td>
                            <td style="display: table-cell;">
                           
                            <div class="btn-group" style="display: inline-block; margin-bottom: -4px;" >
                                <a class="buttonM bDefault" data-toggle="dropdown" href="#">
                                    <input type='hidden' name='add[0][ico]' size='15'>
                                    <span><img src="../images/icons/usual/icon-add.png" /></span><span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" style="width:57px;min-width:57px;">
                                    <li><span><img src="../images/icons/usual/icon-add.png"/></span></li>
                                    <li><span><img src="../images/icons/usual/icon-trash.png" /></span></li>
                                    <li><span><img src="../images/icons/usual/icon-pencil.png" /></span></li>
                                    <li><span><img src="../images/icons/usual/icon-heart.png" /></span></li>
                                </ul>
                            </div>
                            
                            </td>
                            <td style="display: table-cell;"><input type='text' name='add[0][sort]' size='3'></td>
                        </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>    
                    </tfoot>
                </table>
                <button onclick="return false;" class="buttonL bDefault mb10 mt5" style="margin-left:12px;" id='add'><span class="icos-add"></span><span>继续添加</span></button>
                <button onclick="return false;" class="buttonL bDefault mb10 mt5" style="margin-left:12px;" id='subtract'><span class="iconb" data-icon=""></span><span>撤消添加</span></button>
                <button class="buttonL bDefault mb10 mt5" style="margin-left:12px;" type="submit"><span class="icos-refresh2"></span><span>提交更新</span></button>
                <button class="buttonL bDefault mb10 mt5" style="margin-left:12px;" id='delete'><span class="icos-trash"></span><span>删除所选</span></button>
                
                </div>
            <div class="clear"></div> 
        </div>
        <!--orders ends-->
    </div>
    {{ Form::close() }}
    <!-- Main content ends -->
    
</div>
<!-- Content ends -->
@endsection