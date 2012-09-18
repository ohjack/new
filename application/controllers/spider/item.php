<?php
/**
 * 抓取订单产品
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:item.php  2012年09月17日 星期一 17时10分04秒Z $
 */
class Spider_Item_Controller extends Base_Controller {
    
    public $restful = true;

    public function get_index() {

        $user_platforms = User::getPlatforms(1);

        $result = Item::spiderItems( $user_platforms );

        if($result['status'] == 'success' && Session::get('step', 'spiderOrder') == 'spiderOrder') Session::put('step', 'mapSetting');

        return Response::json($result);
    }
}

?>
