<?php

class Skumap_Manage_Controller extends Base_Controller {

    public $restful = true;


    public function get_index() {

        $options = [
            'original_sku' => Input::get('original_sku'),
            'target_sku'   => Input::get('target_sku'),
            'logistics'    => Input::get('logistics'),
            ];
    
        $maps = SkuMap::getMaps(20, $options);
    
        $logistics = array_merge(['' => '--请选择--'], Config::get('application.logistics'));

        return View::make('skumap.manage.list')->with('maps', $maps)
                                               ->with('options', $options)
                                               ->with('logistics', $logistics)
                                               ->with('title', ' 产品设置管理');
    }

    public function put_index() {

        $options = ['id' => Input::get('id', 0)];

        $data = [
            'product_name'  => Input::get('product_name'),
            'product_price' => Input::get('product_price'),
            'original_sku'  => Input::get('original_sku'),
            'target_sku'    => Input::get('target_sku'),
            'logistics'     => Input::get('logistics'),
            ];

        $status = SkuMap::updateMap($options, $data);

        return Response::json($status);
    }

    public function delete_index() {
        $options = ['id' => Input::get('id', 0)];

        $status = SkuMap::deleteMap($options);

        return Response::json($status);
    }
}

?>
