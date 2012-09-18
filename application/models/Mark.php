<?php

class Mark {

    /**
     * 获取用户设置的mark
     *
     * @param: $user_id integer 用户ID
     *
     * return array
     */
    public static function getByUserId( $user_id ) {
        return DB::table('mark')->where('user_id', '=', $user_id)
                                ->order_by('sort', 'desc')
                                ->get();
    }

    /**
     * 通过订单ID获取mark
     *
     * @param: $order_id integer 订单ID
     *
     * return array
     */
    public static function getByOrderId( $order_id ) {
        return DB::table('orders_mark')->left_join('mark', 'mark.id', '=', 'orders_mark.mark_id')
                                       ->where('orders_mark.order_id', '=', $order_id)
                                       ->get(['mark.id', 'mark.name', 'mark.color']);
    }

    /**
     * 保存订单标识
     *
     * param: $data array 订单标识数据
     *
     * return void
     */
    public static function saveOrderMark( $data ) {
        DB::table('orders_mark')->insert( $data );;
    }

    /**
     * 删除订单标识
     *
     * @param: $order_id integer 订单ID
     * @param: $mark_id  integer 标识ID
     *
     * return void
     */
    public static function delOrderMark($order_id, $mark_id) {
        DB::table('orders_mark')->where('order_id', '=', $order_id)
                                ->where('mark_id', '=', $mark_id)
                                ->delete();
    }
}
?>
