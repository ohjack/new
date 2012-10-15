<?php

class Task_Report_Stock {

    // 操作入口
    public function __construct( $args ) {

        // 抓取库存信息
        if(empty($args)) {
            $this->_spider_all();
        } else {
            foreach ($args as $user_id) {
                $user_id = intval($user_id) ? intval($user_id) : 0;
                $this->_spider_one( $user_id );
            }
        }
    }

    // 遍历所有用户获取存库
    private function _spider_all() {
        $user_ids = [1];

        foreach( $user_ids as $user_id ) {
            static::_spider_one( $user_id );
        }
    }

    // 单个用户获取存库
    private function _spider_one( $user_id ) {
        if(empty($user_id)) return ;

        $user_platforms = User::getPlatforms($user_id);
        Stock::importStock( $user_platforms );
    }
}
?>
