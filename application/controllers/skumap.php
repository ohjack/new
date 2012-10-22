<?php

class Skumap_Controller extends Base_Controller {

    public $restful = true;

    public function get_index() {
        $user_id = Sentry::user()->get('id');

        // 获取问题SKU
        $items = Item::getNoSkuItems( $user_id );

        return View::make('skumap.list')->with('items', $items)
                                        ->with('title', '产品设置');
    }

    public function post_index() {

        $datas = Input::get();
        $user_id = Sentry::user()->get('id');

        // validation
        $rules = [
            'original_sku' => 'required|min:1',
            'target_sku'   => 'required|min:1',
            'logistics'    => 'required|min:1'
            ];
        if( isset( $datas['original_sku'] ) ) {
            foreach ($datas['original_sku'] as $key => $value) {
                $data = [
                    'product_name'  => $datas['product_name'][$key],
                    'product_price' => $datas['product_price'][$key],
                    'target_sku'    => $datas['target_sku'][$key],
                    'original_sku'  => $datas['original_sku'][$key],
                    'logistics'     => $datas['logistics'][$key],
                    'user_id'       => $user_id,
                    ];

                $validation = Validator::make($data, $rules);

                if( !$validation->fails() && !SkuMap::chkMap($data['original_sku'], $data['logistics']) ) {
                    SkuMap::saveMap($data);
                }
            }
        }

        Order::Match( $user_id );

        // 获取问题SKU 出来完成允许下一步
        $items = Item::getNoSkuItems($user_id);
        if(empty($items)) {
            return Redirect::to('order/handle');
        } else {
            return Redirect::to('skumap');
        }

    }
}
?>
