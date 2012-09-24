<?php
/**
 * 同步订单状态
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:Orders.php  2012年09月20日 星期四 14时59分25秒Z $
 */

class Rsync_Orders {

    private $_order;

    public function __construct( $order ) {
        $this->_order = $order;
    }

    public function confirmOrders( $options, $order_id ) {
        return $this->_order->confirmOrders( $options, $order_id );
    } 

}

?>
