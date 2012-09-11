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

        $validation = Validator::make($datas, $rules);

        if($validation->fails()) {
            $return = 'error';
        } else {
            if(SkuMap::chkMap($datas['original_sku'], $datas['logistics'])) {
                $return = 'exists';
            } else {
                SkuMap::saveMap($datas);
                $return = 'ok';
            }
        }

        return Response::json($return);
    }
}
?>
