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

    // 添加订单标识
    public function action_addmark() {
        if( !Request::ajax() ) {
            return Response::error('404');
        }

        $data = [
            'order_id' => Input::get('order_id'),
            'mark_id'  => Input::get('mark_id')
            ];

        Mark::saveOrderMark( $data );

        return Response::json('ok');
    }

    // 删除订单标识
    public function action_delmark() {
        if( !Request::ajax() ) {
            return Response::error('404');
        }

            $order_id = Input::get('order_id');
            $mark_id  = Input::get('mark_id');
            $user_id  = 1;   // 当前用户
        
        Mark::delOrderMark( $order_id, $mark_id );

        return Response::json('ok');
    }
}
?>
