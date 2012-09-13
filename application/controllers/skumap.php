<?php

class Skumap_Controller extends Base_Controller {

    public $restful = true;

    public function get_index() {

        // 获取问题SKU
        $items = Item::getNoSkuItems(10);

        return View::make('skumap.list')->with('items', $items);
    }

    public function post_index() {

        $datas = Input::get();

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
                    'logistics'     => $datas['logistics'][$key]
                    ];

                $validation = Validator::make($data, $rules);

                if( !$validation->fails() && !SkuMap::chkMap($data['original_sku'], $data['logistics']) ) {
                    SkuMap::saveMap($data);
                }
            }
        }

        return Redirect::to('skumap');
    }
}
?>
