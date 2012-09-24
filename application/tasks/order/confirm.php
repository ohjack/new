<?php

class Task_Order_Confirm {

    // 操作入口
    public function __construct( $args ) {

        // 确认订单
        if(empty($args)) {
            $this->_confirm_all();
        } else {
            foreach ($args as $user_id) {
                $user_id = intval($user_id) ? intval($user_id) : 0;
                $this->_confirm( $user_id );
            }
        }
    }

    // 遍历所有用户发货
    private function _confirm_all() {

        //$user_ids = DB::table('users')->list('id');

        $user_ids = [1];

        foreach ($user_ids as $user_id) {
            static::_confirm( $user_id);
        }

    }

    // 某个用户的订单确认发货
    private function _confirm( $user_id ) {
        if(empty($user_id)) return ;

        $user_platforms = User::getPlatforms($user_id);
        Order::confirmOrders( $user_platforms );

    }

}
?>
