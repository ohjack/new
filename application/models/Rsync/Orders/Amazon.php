<?php

class Rsync_Orders_Amazon {

    private $_Url;
    
    const SERVER_VERSION = '2009-01-01';
    
    public function confirmOrders( $options, $order) {

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

        $message_id = str_replace('.', '', microtime(true));
        $order_id = $order->entry_id;
        $timestamp = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());

        // 遍历发货信息
        $item_info = '';
        foreach($order->items as $item) {
            $company     = $item->company;
            $method      = Config::get('application.logistic_company')[$item->company]['method'][$item->method];
            $tracking_no = $item->tracking_no;
            $item_info .= <<<EOD
<Item>
    <MerchantOrderItemID>{$item->entry_id}</MerchantOrderItemID>
    <MerchantFulfillmentItemID>{$item->entry_id}</MerchantFulfillmentItemID>
    <Quantity>{$item->shipped_quantity}</Quantity>
</Item>
EOD;

        }

        $feed = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
    <Header>
        <DocumentVersion>1.01</DocumentVersion>
        <MerchantIdentifier>My Store</MerchantIdentifier>
    </Header>
    <MessageType>OrderFulfillment</MessageType>
    <Message>
        <MessageID>{$message_id}</MessageID>
        <OrderFulfillment>
            <MerchantOrderID>{$order_id}</MerchantOrderID>
            <MerchantFulfillmentID>{$order_id}</MerchantFulfillmentID>
            <FulfillmentDate>{$timestamp}</FulfillmentDate>
            <FulfillmentData>
                <CarrierCode>{$company}</CarrierCode>
                <ShippingMethod>{$method}</ShippingMethod>
                <ShipperTrackingNumber>{$tracking_no}</ShipperTrackingNumber>
            </FulfillmentData>
            {$item_info}
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

        // 对应修改的订单状态
        $status_map = [
            '2' => '5',
            '3' => '6',
            '4' => '7',
            ];

        $update = [];
        if( $data['httpcode'] == 200) {
            $update = [ 'order_status' => $status_map[$order->order_status] ]; // 更新订单状态
        }

        return $update;
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
