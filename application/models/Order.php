<?php

class Order {
    
    /**
     * 获取订单
     *
     * @param: $per_page integer 每页记录数
     *
     * reutrn object
     */
    public static function getOrders( $per_page ) {

        $orders = DB::table('orders')->order_by('shipment_level', 'ASC')
                                     ->paginate( $per_page );

        return $orders;
    
    }

    /**
     * 通过订单ID获取订单下的产品
     *
     * @param: $order_id integer 订单ID
     *
     * return object
     */
    public static function getItems($order_id) {

        $items = DB::table('items')->where('order_id', '=', $order_id)->get();

        return $items;
    
    }

    /**
     * 给订单分配物流
     *
     * @param: $order_id integer 订单ID
     * @param: $logistics string 物流系统名称
     *
     * return void
     */
    public static function setLogistics( $order_id, $logistics ) {

        $option = [
            'order_status' => $logistics
            ];
    
        DB::table('orders')->where('id', '=', $order_id)
                           ->update( $option );
    }

}
?>
