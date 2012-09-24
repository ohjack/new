<?php
/**
 * 抓取订单
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:SpiderOrders.php  2012年08月28日 星期二 14时40分40秒Z $
 */

class Spider_Orders {
    
    private $_order;

    public function __construct($order){
        $this->_order = $order;
    }

    public function getOrders( $option ) {
        return $this->_order->getOrders( $option );
    }

    public function getItems( $option ) {
        return $this->_order->getItems( $option );
    }
}
?>