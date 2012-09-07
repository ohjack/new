<?php

class Order {
    
    public static function getOrders($per_page) {

        $orders = DB::table('orders')->where('order_status', '=', 'unhandle')
                                     ->order_by('shipment_level', 'ASC')
                                     ->paginate($per_page);

        return $orders;
    
    }

    public static function getItems($order_id) {

        $items = DB::table('items')->where('order_id', '=', $order_id)->get();

        return $items;
    
    }

    public static function setLogistics( $order_id, $logistics ) {

        $option = [
            'order_status' => $logistics
            ];
    
        DB::table('orders')->where('id', '=', $order_id)
                           ->update( $option );
    }

}
?>
