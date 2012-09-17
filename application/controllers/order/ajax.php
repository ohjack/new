<?php

class Order_Ajax_Controller extends Base_Controller {


    public function action_index() {
    
    
    }

    // 获取订单详情
    public function action_info() {
        if( !Request::ajax() ) {
            return Response::error('404');
        }

        $order_id = Input::get('order_id');
        $order = Order::getOrder( $order_id );

        return Response::json($order);
        
    }
}
?>
