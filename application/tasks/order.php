<?php
/**
 * 订单后台处理命令行任务
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:order.php  2012年09月27日 星期四 16时04分12秒Z $
 */
class Order_Task extends Task {


    /**
     * 命令入口
     *
     * @param: $arguments array 命令参数
     *
     * return viod
     */
    public function run( $arguments = [] ) {
        if (! count($arguments)) $this->_help();


        $command = ($arguments[0] !=='') ? $arguments[0] : 'help';
        $args = array_slice($arguments, 1);

        switch($command) {
            case "confirm":
            case "c":
                new Task_Order_Confirm($args);
                break;
            case "spider":
            case "s":
                new Task_Order_Spider($args);
                break;
            case "match":
            case "m":
                new Task_Order_Match($args);
                break;
            default:
                $this->_help();
                break;
        }
    }

    /**
     * 帮助
     *
     */
    private function _help() {
        echo '帮助 ：';
        echo "\torder <命令> [参数] [会员ID...]\n";
        echo "命令：\n";
        echo "\ts/spider\t抓取订单\n";
        echo "\tm/match\t\t订单根据规则匹配物流\n";
        echo "\tc/confirm\t同步发货状态\n";

        exit();
    }
}
?>
