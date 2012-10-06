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
    public static function save( $data ) {
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
    public static function delete($order_id, $mark_id) {
        DB::table('orders_mark')->where('order_id', '=', $order_id)
                                ->where('mark_id', '=', $mark_id)
                                ->delete();
    }
    
    /**
     * 添加用户标识
     *
     * param: $data array 订单标识数据
     *
     * return void
     */
    public static function add( $data ) {
        DB::table('mark')->insert( $data );;
    }
    
    /**
     * 更新用户标识
     * 
     * parem: $date array 标识数据
     * param: $mark_id    标识ID
     * 
     * return void
     */
    public static function update($mark_id,$data)
    {
        DB::table('mark')->where('id','=',$mark_id)
                            ->update($data);
    }
    

    /**
     * 删除用户标识
     * 
     * @param array $mark_id
     * 
     * return void
     */
    public static function delFromMarkTable($mark_id)
    {
        DB::table('mark')->where_in('id',$mark_id)->delete();
    }
    
    
}
?>
