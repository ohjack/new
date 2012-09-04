<?php

class Sku_Map_Controller extends Base_Controller {

    public $restful = true;

    public function get_index() {
        echo 'map';
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
            if(SkuMap::chkMap($datas)) {
                $return = 'exists';
            } else {
                SkuMap::saveMap($datas);
                $return = 'ok';
            }

        }

        echo json_encode($return);
        return ;
        
    }
}
?>
