<?php

class Order_Logistics_Controller extends Base_Controller {

    public $restful = true;

    public function post_index() {

        $action = Input::get('action');

        $result = Logistics::allHandle();

        Session::put('step', 'handleLogistics');

        return Response::json($result);
    }

    public function get_index() {

        $systems = [
            'coolsystem',
            'birdsystem',
            ];

        $result = [
            'status'  => 'success',
            'message' => []
            ];

        $files = Logistics::getCsvFile( $systems );

        if( !empty($files) ) {
            $result['message'] = $files;
        }

        return Response::json($result);
    }
}
?>
