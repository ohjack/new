<?php
/**
 * 订单抓取
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:spider.php  2012年09月25日 星期二 11时25分20秒Z $
 */


class Task_Order_Spider {

    // 操作入口
    public function __construct( $args ) {

        // 确认订单
        if(empty($args)) {
            $this->_spider_all();
        } else {
            foreach ($args as $user_id) {
                $user_id = intval($user_id) ? intval($user_id) : 0;
                $this->_spider_one( $user_id );
            }
        }
    }

    // 遍历所有用户抓取订单
    private function _spider_all() {

        //$user_ids = DB::table('users')->list('id');

        $user_ids = [1];

        foreach ($user_ids as $user_id) {
            static::_spider_one( $user_id);
        }

    }

    // 单个用户的订单抓取
    private function _spider_one( $user_id ) {
        if(empty($user_id)) return ;

        //$user_platforms = User::getPlatforms($user_id);
        //Order::confirmOrders( $user_platforms );

    }

}
?>
