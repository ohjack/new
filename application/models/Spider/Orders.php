<?php
/**
 * 抓取订单适配器
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:SpiderOrders.php  2012年08月28日 星期二 14时40分40秒Z $
 */

class Spider_Orders {
    
    private $_order;

    /**
     * 初始化适配器
     *
     * @param: $order object 适配器
     *
     * return object
     */
    public function __construct($order){
        $this->_order = $order;
    }

    /**
     * 抓取订单
     *
     * @param: $option array API参数
     *
     * return array
     */
    public function getOrders( $option ) {
        return $this->_order->getOrders( $option );
    }

    /**
     * 抓取产品
     *
     * @param: $option array
     *
     * return array
     */
    public function getItems( $option ) {
        return $this->_order->getItems( $option );
    }

    /**
     * 获取产品API参数
     *
     * @param: $base_option array API基本参数
     *
     * return array
     */
    public function getItemOption( $base_option ) {
        return $this->_order->getItemOption( $base_option );
    }

    /**
     * 获取订单API参数
     *
     * @param: $platform_id interge 平台ID
     * @param: $base_option array   API基本参数
     *
     * return array
     */
    public function getOrderOption( $platform_id, $base_option ) {
        return $this->_order->getOrderOption($platform_id, $base_option);
    }
}
?>
