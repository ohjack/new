<?php
/**
 * 抓取订单
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:order.php  2012年09月17日 星期一 17时10分19秒Z $
 */

class Spider_Order_Controller extends Base_Controller {
    
    public $restful = true;

    public function get_index() {

        $user_platforms = User::getPlatforms(1);

        $result = Order::spiderOrders( $user_platforms );

        return Response::json($result);

    }
}
?>
