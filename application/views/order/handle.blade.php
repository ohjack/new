@layout('layout')
@section('script')
{{ HTML::script('js/order.js') }}
{{ HTML::script('js/ajaxfileupload.js') }}
{{ HTML::script('js/float.js') }}
@endsection  
@section('content')
<div style="margin: 100px 0 100px 100px;">
  @foreach(Config::get('application.steps') as $step)
    <div class="step">
        <a href="{{ $step['link'] }}" id="{{ $step['id'] }}" class="{{ $step['class'] }}">{{ $step['name'] }}</a>
    </div>
  @endforeach
</div>
<div style="clear: both"></div>
<div class="add_logistics_info">
    <div class="title"><em>x</em>添加物流信息</div>
    <div>
        <span style="float: right;">订单ID：<input name="keyword" value=''/> <input id="logistic_search" type="button" value="搜索"></span>
        <form name="form" action="" method="POST" enctype="multipart/form-data">
            导入文件<input id="import_file" name="import_file" type="file"/><input type="button" value="上传" id="import_logistic"><span style="display: none" id="upload_tips"></span><a href="#">下载导入模板</a>
        </form>
    </div>
    <form id="logistic_form">
    <table id="add_logistics" style="width: 960px">
        <thead>
            <tr>
              <th>订单ID</th>
              <th>物流公司</th>
              <th>物流方式</th>
              <th>跟踪号</th>
              <th>是否提前发货</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5">没有产品</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
              <td colspan="8"><span style="float: right"><input type="button" value="提交" id="logistic_submit" /></span><div class="pagination"></div></td>
            </tr>
        </tfoot>
    </table>
    </form>
</div>
@endsection  
