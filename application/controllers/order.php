<?php

class Order_Controller extends Base_Controller {

    public $restful = true;

    public function get_index() {

        Session::put('step', 'spiderOrder');

        // 获取订单列表
        $options = [
            
            ];

        $orders = Order::getOrders(15, $options);

        $logistics = array_keys(Config::get('application.logistics'));

        foreach ($orders->results as $order) {
            if( $order->order_status == 'unhandle') {
                $order->order_status = '待处理';
            } else if($order->order_status == 'handled') {
                $order->order_status = '已经处理';
            } else if(in_array($order->order_status, $logistics)) {
                $order->order_status = '已匹配物流<br />' . $order->order_status;
            }
        }

        return View::make('order.list')->with('orders', $orders)
                                       ->with('title', '订单列表');
    
    }

    public function post_orders() {

    }
}
?>
