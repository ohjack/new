<?php

class Rsync_Orders_Amazon {

    private $_Url;
    
    const SERVER_VERSION = '2009-01-01';
    
    public function confirmOrders( $option ) {
        $option = [
            'AWSAccessKeyId' => 'AKIAJGUMF5LENLIW6ZAQ',
            'Merchant'       => 'A3LMXTNFZ71A3Q',
            'Key'            => 'jRa5CBIrZVTMm+GD9wwSNSQ+vwpyflw1eUn6aebL',
            'Version'        => self::SERVER_VERSION,
            'Action'         => 'SubmitFeed',
            'FeedType'       => '_POST_FLAT_FILE_ORDER_ACKNOWLEDGEMENT_DATA_',
            'MarketplaceIdList.Id.1' => 'ATVPDKIKX0DER',
            'PurgeAndReplace' => 'false',
        ];

        $this->_Url = 'https://mws.amazonservices.com/';

        $param = $this->_getParam( $option );
        $body = '<?xml version="1.0" encoding="utf-8"?>';
        $fh = fopen('php://memory', 'rw+');
        fwrite($fh, $body);
        $param['content_md5'] = base64_encode(md5(stream_get_contents($fh), true));
        fclose($fh);
        //echo $body;

        $curl = new Amazon_Curl();
        $curl -> setParam( $param );
        $param = $curl->perform();

        return $param;
    
    }

    private function _getParam( $option ) {
    
        $amazon = new Amazon();

        $amazon -> setData( $option, $this->_Url);

        $data = $amazon -> combine();

        $param = [
                'url'   => $this->_Url,
                'query' => $data,
            ];

        return $param;
    }
}
?>
