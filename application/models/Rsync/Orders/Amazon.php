<?php

class Rsync_Orders_Amazon {

    private $_Url;
    
    const SERVER_VERSION = '2009-01-01';
    
    public function confirmOrders( $options, $order) {

        if(empty($order)) return;

        $option = [
            'AWSAccessKeyId'         => $options['AWSAccessKeyId'],
            'Merchant'               => $options['SellerId'],
            'Key'                    => $options['Key'],
            'Version'                => self::SERVER_VERSION,
            'Action'                 => 'SubmitFeed',
            'FeedType'               => '_POST_ORDER_FULFILLMENT_DATA_',
            'MarketplaceIdList.Id.1' => $options['MarketplaceId.Id.1'],
            'PurgeAndReplace'        => 'false',
        ];

        $this->_Url = $options['Server'];
        unset($options['Server']);

        $param = $this->_getParam( $option );

        $message_id  = str_replace('.', '', microtime(true));
        $order_id    = $order->entry_id;
        $company     = $order->company;
        $method      = $order->method;
        $tracking_no = $order->tracking_no;
        $timestamp   = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());

        $filename = path('public') . 'data/rsync/' . md5(time() . rand(0,1000)) . '.xml';

        // -------------------------------XML file -----------------------------------
        $xml = new XMLWriter();
        $xml->openUri($filename);
        $xml->setIndentString('    ');
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'utf-8');
        $xml->startElement('AmazonEnvelope');
        $xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml->writeAttribute('xsi:noNamespaceSchemaLocation', 'amzn-envelope.xsd');
        $xml->startElement('Header');
        $xml->startElement('DocumentVersion');
        $xml->text('1.01');
        $xml->endElement();
        $xml->startElement('MerchantIdentifier');
        $xml->text($options['MarketplaceId.Id.1']);
        $xml->endElement();
        $xml->endElement();
        $xml->startElement('MessageType');
        $xml->text('OrderFulfillment');
        $xml->endElement();
        $xml->startElement('Message');
        $xml->startElement('MessageID');
        $xml->text(1);
        $xml->endElement();
        $xml->startElement('OrderFulfillment');
        $xml->startElement('AmazonOrderID');
        $xml->text($order_id);
        $xml->endElement();
        $xml->startElement('FulfillmentDate');
        $xml->text($timestamp);
        $xml->endElement();
        $xml->startElement('FulfillmentData');
        $xml->startElement('CarrierCode');
        $xml->text($company);
        $xml->endElement();
        $xml->startElement('ShippingMethod');
        $xml->text($method);
        $xml->endElement();
        $xml->startElement('ShipperTrackingNumber');
        $xml->text($tracking_no);
        $xml->endElement();
        $xml->endElement();
        foreach($order->items as $item) {
            $xml->startElement('Item');
            $xml->startElement('AmazonOrderItemCode');
            $xml->text($item->entry_id);
            $xml->endElement();
            $xml->startElement('Quantity');
            $xml->text($item->quantity);
            $xml->endElement();
            $xml->endElement();
        }
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();
        $xml->endDocument();
        // -------------------------------XML file end -------------------------------

        $param['content_md5'] = base64_encode(md5_file($filename, true));
        // chmod($filename, 0755);
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

    private function _xml2Array( $xml ) {
        return json_decode(json_encode((array) simplexml_load_string( $xml )), 1);
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
