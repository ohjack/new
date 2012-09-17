<?php
/**
 * Amazon 平台类
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:Amazon.php  2012年09月17日 星期一 19时39分47秒Z $
 */

class Platform_Amazon {

    /**
     * 抓取订单API配置
     */
    public function getOrderOption( $platform_id, $option ) {

        $lasttime = SpiderLog::getLastSpider('order', $platform_id);
        if( !$lasttime ) return;

        // 额外的option
        $option['CreatedAfter'] = $lasttime;
        $option['OrderStatus.Status.1'] = 'Unshipped';
        $option['OrderStatus.Status.2'] = 'PartiallyShipped';
        $option['Action'] =  'ListOrders';
        
        return $option;

    }

    /**
     * 抓取产品API配置
     *
     * @param: $option array 基础配置
     *
     * return array amazon配置
     */
    public function getItemOption( $option ) {
        $option['AmazonOrderId'] = $option['order_id'];
        $option['Action']        = 'ListOrderItems';
        unset($option['order_id']);
        unset($option['MarketplaceId.Id.1']);

        return $option;
    }
}

?>
