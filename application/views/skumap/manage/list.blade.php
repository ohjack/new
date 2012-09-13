@layout('layout')
@section('script')
{{ HTML::script('js/skumap.js') }}
@endsection
@section('content')
<div><a href="{{ URL::to('skumap') }}"  style="float: right; margin-right: 10px">返回产品设置</a></div>
<div id="search">
    {{ Form::open('skumap/manage', 'GET') }}
    <table>
    <tr>
        <th style="width: 80px">原SKU:</th>
        <td>{{ Form::text('original_sku') }}</td>
    </tr>
    <tr>
        <th>映射SKU:</th>
        <td>{{ Form::text('target_sku') }}</td>
    </tr>
    <tr>
        <th>物流:</th>
        <td>
            <select name="logistics">
                @foreach($logistics as $key=>$value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach;
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="2">{{ Form::button('搜索') }}</td>
    </tr>
    </table>
    {{ Form::close() }}
</div>
    <table class="table">
      <thead>
        <tr>
          <th width="10%">SKU</th>
          <th>品名</th>
          <th width="10%">价值</th>
          <th width="15%">映射SKU</th>
          <th width="10%">物流</th>
          <th width="15%">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($maps->results as $map)
        <tr id="skumap{{$map->id}}">
          <td>{{ $map->original_sku }}</td>
          <td>{{ Form::text('product_name'.$map->id, $map->product_name) }}</td>
          <td>{{ Form::text('product_price'.$map->id, $map->product_price) }}</td>
          <td>
              {{ Form::hidden('original_sku'.$map->id, $map->original_sku) }}
              {{ Form::text('target_sku'.$map->id, $map->target_sku) }}
          </td>
          <td>
              {{ Form::select('logistics'.$map->id, Config::get('application.logistics'), $map->logistics) }}
          </td>
          <td>
            {{ Form::button('更新', ['key' => $map->id, 'class' => 'update_skumap']) }}
            {{ Form::button('删除', ['key' => $map->id, 'class' => 'delete_skumap']) }}
            <span id="tips{{$map->id}}"></span>
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="6">{{ $maps->appends($options)->links() }}</td>
        </tr>
      </tfoot>
    </table>
@endsection
