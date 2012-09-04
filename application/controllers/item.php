<?php

class Item_Controller extends Base_Controller {

    public $restful = true;

    public function get_index() {
    
        $order_id = $_GET['order_id'] ? $_GET['order_id'] : 0;
        $items = Order::getItems($order_id);

        echo json_encode($items);

        return;
    }
}
?>
