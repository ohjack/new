<?php

class CurlException extends Exception {

    private $_errorInfo;

    public function __construct($errorInfo) {
        $this->_errorInfo = $errorInfo;
    }

    public function getError() {
        return $this->_errors($this->_errorInfo);
    }

    private function _errors($index) {
        $errors = [
            '400' => '错误请求',
            '401' => '未授权',
            '403' => '已禁止',
            '404' => '未找到页面',
            '405' => '方法禁用',
            '406' => '不接受',
            '407' => '需要代理授权',
            '408' => '请求超时',
            '409' => '冲突',
            '410' => '已删除',
            '411' => '需要有效长度',
            '412' => '未满足前提条件',
            '413' => '请求实体过大',
            '414' => '请求URL过长',
            '415' => '不支持的媒体类型',
            '416' => '为满足期望值',
            '500' => '服务器内部错误',
            '501' => '尚未实施',         // retry
            '502' => '错误网关',
            '503' => '服务不可用',       // retry
            '504' => '网关超时',
            '505' => 'HTTP版本不受支持'
            ];

        return $index . ' ' . $errors[$index];
    
    }

}
?>
