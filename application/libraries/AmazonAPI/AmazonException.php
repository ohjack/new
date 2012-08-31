<?php

class AmazonException extends Exception {

    private $_errorInfo;

    public function __construct($errorInfo) {
        $this->_errorInfo = $errorInfo;
    }

    public function getError() {
        return $this->_errorInfo;
    }
}

?>
