<?php

class Order_Logistics_Controller extends Base_Controller {

    public $restful = true;

    public function post_index() {

        $action = Input::get('action');

        Logistics::allHandle();
        Session::put('step', 'handleLogistics');

        return Response::json('ok');
    }

    public function get_index() {

        $system = Input::get('system');

        $systems = [
            'coolsystem',
            'birdsystem',
            ];

        if(in_array($system, $systems)) {
            Logistics::getCSV($system);
        }else if($system == 'micaosystem') {
            exit('micaosystem');
        }else {
            
            $coolsystem_count = Order::countLogistics('coolsystem');
            $birdsystem_count = Order::countLogistics('birdsystem');
            $micaosystem_count = Order::countLogistics('micaosystem');

            if($coolsystem_count + $birdsystem_count + $micaosystem_count == 0) {
                Session::put('step', 'spiderOrder'); 
                return Redirect::to('order');
            }

            return View::make('order.logistics.download')->with('coolsystem_count', $coolsystem_count)
                                                         ->with('birdsystem_count', $birdsystem_count)
                                                         ->with('micaosystem_count', $micaosystem_count);
        }
    }
}
?>
