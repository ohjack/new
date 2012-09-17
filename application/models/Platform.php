<?php
/**
 * 平台类
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:Amazon.php  2012年09月17日 星期一 19时39分47秒Z $
 */

class Platform {

    private $_platform;

    public function __construct( $platform ) {
        $this->_platform = $platform;
    }

    public function getOrderOption( $platform_id, $option ) {
        return $this->_platform->getOrderOption( $platform_id, $option );
    }

    public function getItemOption( $option ) {
        return $this->_platform->getItemOption( $option );
    }
}

?>
