<?php

/**
 * 获取亚马逊报告
 * 
 * 
 * 
 */
class Spider_Report_Amazon {

    private $_AWSAccessKeyId;
    private $_SellerId;
    private $_Key;
    private $_Url;
    private $_Type;

    const SERVER_VERSION = '2009-01-01';  // API版本

    /**
     * 获取存库信息
     *
     * @param: $options array API设置
     *
     * return array 库存信息
     */
    public function getStockReport( $options ) {
        $options['ReportType'] = '_GET_AFN_INVENTORY_DATA_';
        $options['Version'] = self::SERVER_VERSION;
        $this->_setOptions( $options );
        unset($options['Server']);

        // 请求库存报告
        $options['Action']  = 'RequestReport';

        $report_id = $this->_requestReport( $options );

        echo "report id: {$report_id} \n";

        // 得到存库报告
        if($report_id) {
            $options['Action'] = 'GetReportRequestList';
            $options['ReportRequestIdList.Id.1'] = $report_id;
            $status = $this->_getReportStatus( $options );
            if($status['status'] == 'done' && isset($status['id'])) {
                $options['ReportId'] = $status['id'];
                $options['Action'] = 'GetReport';
                $data = $this->_getReport( $options );
                if($data['httpcode'] == 200) {
                    $datas = explode("\n", $data['data']);
                    array_shift($datas);
                    $insert_datas = [];
                    foreach($datas as $data) {
                        $data_items = explode("\t", $data);
                        $insert_datas[] = [
                            'sku'              => $data_items[0],
                            'fulfillment_sku'  => $data_items[1],
                            'fulfillment_asin' => $data_items[2],
                            'status'           => $data_items[4] == 'SELLABLE' ? 1 : 0,
                            'quantity'         => $data_items[5],
                            ];
                    }

                    return $insert_datas;
                }
            }
        }
    }

    /**
     * 获取报告状态
     *
     * @param: $options API参数
     *
     * return array status
     */
    private function _getReportStatus( $options ) {
        echo "wait 46sec...\n";
        sleep(46); // 亚马逊45秒恢复一个请求

        $param = $this->_getParam($options);

        $curl = new Amazon_Curl();
        $curl->setParam($param);
        $data = $curl->perform();
        $status = [];
        if( $data['httpcode'] == 200) {
            $data = $this->_xml2Array( $data['data'] );

            if(isset($data['GetReportRequestListResult']['ReportRequestInfo']['ReportProcessingStatus'])) {
                $request_status = $data['GetReportRequestListResult']['ReportRequestInfo']['ReportProcessingStatus'];
                echo "The request status: {$request_status}\n";
                if( $request_status == '_DONE_') {
                    $status['status'] = 'done';
                    $status['id']     = $data['GetReportRequestListResult']['ReportRequestInfo']['GeneratedReportId'];
                    return $status;
                } else {
                    if( $request_status == '_DONE_NO_DATA_' ) return $status['status'] = 'done';
                    $status = $this->_getReportStatus($options);
                    if( $status ) return $status;
                }
            }
        }
    }

    /**
     * 设置基本API参数
     *
     * @param: $options array API参数
     *
     * return viod
     */
    private function _setOptions($options) {
        $this->_AWSAccessKeyId = $options['AWSAccessKeyId'];
        $this->_SellerId       = $options['SellerId'];
        $this->_Key            = $options['Key'];
        $this->_Url            = $options['Server'];
    }

    /**
     * 获取API参数
     *
     * @param: $options array 参数
     *
     * return array 转换后的参数
     */
    private function _getParam( $options ) {
        $amazon = new Amazon();

        $amazon->setData( $options , $this->_Url );
        $data = $amazon -> combine();

        $param = [
            'url'   => $this->_Url,
            'query' => $data,
            ];

        return $param;
    }

    /**
     * 生成报告请求
     *
     * @param: $options array 参数
     *
     * return integer 请求ID
     */
    private function _requestReport( $options ) {
        $param = $this->_getParam( $options );

        $curl = new Amazon_Curl();
        $curl->setParam($param);
        $data = $curl->perform();

        if( $data['httpcode'] == 200 ) {
            $data = $this->_xml2Array( $data['data'] );
            if(isset($data['RequestReportResult']['ReportRequestInfo']['ReportRequestId']))
                return $data['RequestReportResult']['ReportRequestInfo']['ReportRequestId']; 
        }
    }

    /**
     * 获取报告
     *
     * @param: $options array API参数
     *
     * return array
     */
    private function _getReport( $options ) {
        echo "GET Report\n";
        $param = $this->_getParam( $options );

        $curl = new Amazon_Curl();
        $curl->setParam($param);
        $data = $curl->perform();

        return $data;
    }

    /**
     * XMl内容转换成数组
     *
     * @param: $xml string xml内容
     *
     * return array
     */
    private function _xml2Array( $xml ) {
        return json_decode(json_encode((array) simplexml_load_string( $xml )), 1);
    }
}
?>
