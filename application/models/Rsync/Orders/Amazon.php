<?php

class Rsync_Orders_Amazon {

    private $_Url;
    
    const SERVER_VERSION = '2009-01-01';
    
    public function confirmOrders( $options, $order_id) {

        $option = [
            'AWSAccessKeyId'         => $options['AWSAccessKeyId'],
            'Merchant'               => $options['SellerId'],
            'Key'                    => $options['Key'],
            'Version'                => self::SERVER_VERSION,
            'Action'                 => 'SubmitFeed',
            'FeedType'               => '_POST_FLAT_FILE_ORDER_ACKNOWLEDGEMENT_DATA_',
            'MarketplaceIdList.Id.1' => $options['MarketplaceId.Id.1'],
            'PurgeAndReplace'        => 'false',
        ];

        $this->_Url = $options['Server'];
        unset($options['Server']);

        $param = $this->_getParam( $option );

        $feed = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
    <Header>
        <DocumentVersion>1.01</DocumentVersion>
        <MerchantIdentifier>My Store</MerchantIdentifier>
    </Header>
    <MessageType>OrderFulfillment</MessageType>
    <Message>
        <MessageID>1</MessageID>
        <OrderFulfillment>
            <MerchantOrderID>1234567</MerchantOrderID>
            <MerchantFulfillmentID>1234567</MerchantFulfillmentID>
            <FulfillmentDate>2002-05-01T15:36:33-08:00</FulfillmentDate>
            <FulfillmentData>
                <CarrierCode>UPS</CarrierCode>
                <ShippingMethod>Second Day</ShippingMethod>
                <ShipperTrackingNumber>1234567890</ShipperTrackingNumber>
            </FulfillmentData>
            <Item>
                <MerchantOrderItemID>1234567</MerchantOrderItemID>
                <MerchantFulfillmentItemID>1234567</MerchantFulfillmentItemID>
                <Quantity>2</Quantity>
            </Item>
        </OrderFulfillment>
    </Message>
</AmazonEnvelope>
EOD;
        $filename = path('public') . 'data/rsync/' . md5(time() . rand(0,1000)) . '.xml';
        file_put_contents($filename, $feed);
        chmod($filename, 0755);  
        $param['content_md5'] = base64_encode(md5_file($filename, true));
        $param['filename'] = $filename;

        $curl = new Amazon_Curl();

        $data = $curl -> submitFeed( $param );

        $update = [];
        if( $data['httpcode'] == 200) {
            $update = [ 'order_status' => 0 ]; // 更新订单状态
        }

        return $update

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
