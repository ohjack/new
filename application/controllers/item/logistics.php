<?php

class Item_Logistics_Controller extends Base_Controller {

    public $restful = true;

    public function post_index() {

        $action = Input::get('action');

        if($action == 'allOrder') {
            Logistics::allHandle();
        } else if($action == 'allOther') {
            Logistics::allOther();
        } else if($action == 'listOrder') {
            Logistics::listToOther( Input::get('ids') );
        }

        return Response::json('ok');
    }

    public function get_index() {

        $system = Input::get('system');

        $systems = [
            'coolsystem',
            'birdsystem',
            'other'
            ];

        if(in_array($system, $systems)) {
            Logistics::getCSV($system);
        }else {
            return View::make('item.logistics.download');
        }
    }

    public function put_index() {
    
        $maps = [

            ];

        DB::table('sku_map')->insert($maps);
        echo 'OK';

    }

}
?>
