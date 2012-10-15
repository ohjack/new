<?php

class Spider_Report {
    
    private $_report;

    /**
     * 初始化适配器
     *
     * @param: $report object 适配器
     *
     * reutrn object
     */
    public function __construct($report) {
        $this->_report = $report;
    }

    /**
     * 获取库存报告
     *
     * @param: $option array API参数
     *
     * return array
     */
    public function getStockReport( $options ) {
        return $this->_report->getStockReport( $options );
    
    }

}
?>
