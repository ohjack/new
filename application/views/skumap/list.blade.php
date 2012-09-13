@layout('layout')
@section('script')
{{ HTML::script('js/skumap.js') }}
@endsection
@section('content')
<div><a href="{{ URL::to('skumap/manage') }}"  style="float: right; margin-right: 10px">产品设置管理</a></div>
{{ Form::open('skumap', 'POST') }}
    <table class="table">
      <thead>
        <tr>
          <th width="10%">SKU</th>
          <th>名称</th>
          <th width="10">国家</th>
          <th width="10">来源</th>
          <th width="20%">品名</th>
          <th width="5%">价值</th>
          <th width="15%">映射SKU</th>
          <th width="5%">物流</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
        <tr>
          <td>{{ $item->sku }}</td>
          <td>{{ $item->name }}</td>
          <td>{{ $item->shipping_country }}</td>
          <td>{{ $item->from }}</td>
          <td>{{ Form::text('product_name[]') }}</td>
          <td>{{ Form::text('product_price[]') }}</td>
          <td>
              {{ Form::hidden('original_sku[]', $item->sku) }}
              {{ Form::text('target_sku[]') }}
          </td>
          <td>
              {{ Form::select('logistics[]', Config::get('application.logistics'), $item->logistics) }}
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="8"><input type="submit" value="保存" style="float: right; margin-right: 20px"/></td>
        </tr>
      </tfoot>
    </table>
{{ Form::close() }}
@endsection
