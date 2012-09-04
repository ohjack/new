<?php

class Order {
    
    public static function getOrders($per_page) {

        $orders = DB::table('orders')->order_by('shipment_level', 'ASC')->paginate($per_page);

        return $orders;
    
    }

    public static function getItems($order_id) {

        $items = DB::table('items')->where('order_id', '=', $order_id)->get();

        return $items;
    
    }

}
?>
