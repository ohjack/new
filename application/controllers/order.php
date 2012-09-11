<?php

class Order_Controller extends Base_Controller {

    public $restful = true;

    public function get_index() {

        $orders = Order::getOrders(5);

        return View::make('order.list')->with('orders', $orders);
    
    }

    public function post_orders() {

    }
}
?>
