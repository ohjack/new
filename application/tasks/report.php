<?php

class Report_Task extends Task {


    public function run ( $arguments = [] ) {
    
        if (! count($arguments)) $this->_help();

        $command = ($arguments[0] !=='') ? $arguments[0] : 'help';
        $args = array_slice($arguments, 1);

        switch ($command) {
            case 'stock':
            case 's':
                new Task_Report_Stock($args);
                break;
            default:
                $this->_help();
                break;
        }
    }


    /**
     * help
     */
    private function _help() {
        echo '帮助 ：';
        echo "\treport <命令> [参数] [会员ID...]\n";
        echo "命令:\n";
        echo "\ts/stock\t获取库存信息\n";

    }
}
?>
