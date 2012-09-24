<?php


class Order_Task extends Task {


    public function run( $arguments = [] ) {
        if (! count($arguments)) $this->_help();


        $command = ($arguments[0] !=='') ? $arguments[0] : 'help';
        $args = array_slice($arguments, 1);

        switch($command) {
            case "confirm":
            case "c":
                new Task_Order_Confirm($args);
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
        echo "\torder <命令> [参数] [选项 ..]\n";
        echo "命令：\n";
        echo "\tc/confirm        同步发货状态后面参数跟指定会员ID为空则确认全部会员\n";

        exit();
    }
}
?>
