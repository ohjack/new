@layout('layout')
@section('script')
{{ HTML::script('js/skumap.js') }}
@endsection
@section('content')
    <table class="table">
      <thead>
        <tr>
          <th width="10%">SKU</th>
          <th width="5%">数量</th>
          <th>名称</th>
          <th width="10%">来源</th>
          <th width="20%">SKU设置</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
        <tr id="item{{$item->id}}">
          <td>{{$item->sku}}</td>
          <td>{{$item->count}}</td>
          <td>{{$item->name}}</td>
          <td>{{$item->from}}</td>
          <td>
            <input type="hidden" name="original_sku{{$item->id}}" value="{{$item->sku}}">
            <input type="text" name="target_sku{{$item->id}}">
            <select name="system{{$item->id}}">
              <option value="coolsystem" @if($item->from == 'Amazon.com') selected="true" @endif>酷系统</option>
              <option value="birdsystem" @if($item->from == 'Amazon.co.uk') selected="true" @endif>鸟系统</option>
            </select>
            <input class="sku_map_submit" key="{{$item->id}}" type="button" value="提交" />
            <span id="tips{{$item->id}}"></span>
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5"></td>
        </tr>
      </tfoot>
    </table>
@endsection
