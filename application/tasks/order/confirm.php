<?php

class Task_Order_Confirm {

    public function __construct( $args ) {

        // 确认订单
        if(empty($args)) {
            $this->_confirm_all();
        } else {
            foreach ($args as $user_id) {
                $user_id = intval($user_id) ? intval($user_id) : 0;

                echo $this->_confirm( $user_id );
                echo "\n";
            }
        }

    }

    private function _confirm_all() {

        //$user_ids = DB::table('users')->list('id');

        $user_ids = [1];

        foreach ($user_ids as $user_id) {
            $user_platforms = User::getPlatforms($user_id);

            $result = Order::confirmOrders( $user_platforms );

            print_r($result);

        }


    }

    private function _confirm( $user_id ) {
        if(empty($user_id)) return ;

        return $user_id;
    }

}
?>
