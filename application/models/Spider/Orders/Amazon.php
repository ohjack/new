<?php
/**
 * Amazon 抓取订单类
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:AmazonOrder.php  2012年08月28日 星期二 10时58分15秒Z $
 */

class Spider_Orders_Amazon {

    private $_AWSAccessKeyId;
    private $_SellerId;
    private $_Key;
    private $_Url;

    const SERVER_VERSION = '2011-01-01';  // API版本

    /**
     * 抓取订单API配置
     *
     * @param: $platform_id integer 平台ID
     * @param: $option      array   基本配置
     *
     * return array
     */
    public function getOrderOption( $platform_id, $option ) {

        $lasttime = SpiderLog::getLastSpider('order', $platform_id);
        if( !$lasttime ) return;

        // 额外的option
        $option['CreatedAfter'] = $lasttime;
        $option['OrderStatus.Status.1'] = 'Unshipped';
        $option['OrderStatus.Status.2'] = 'PartiallyShipped';
        $option['Action'] =  'ListOrders';
        
        return $option;

    }

    /**
     * 抓取产品API配置
     *
     * @param: $option array 基础配置
     *
     * return array amazon配置
     */
    public function getItemOption( $option ) {
        $option['AmazonOrderId'] = $option['order_id'];
        $option['Action']        = 'ListOrderItems';
        unset($option['order_id']);
        unset($option['MarketplaceId.Id.1']);

        return $option;
    }

    /**
     * 抓取order信息
     *
     * @param: $option array API参数
     *
     * return array
     */
    public function getOrders( $option ) {

        $this->_AWSAccessKeyId = $option['AWSAccessKeyId'];
        $this->_SellerId = $option['SellerId'];
        $this->_Key = $option['Key'];
        $this->_Url = $option['Server'] . 'Orders/' . self::SERVER_VERSION;
        unset($option['Server']);

        $option['Version'] = self::SERVER_VERSION;

        $param = $this->_getParam($option);

        $curl = new Amazon_Curl();
        $curl -> setParam($param);
        $data = $curl -> perform();

        $listOrders = [];
        if( $data['httpcode'] == 200 ) {
            $listOrders = $this->_getOrdersDataFormat( $this->_getOrdersData($data) );
        } else if ( $data['httpcode'] == 501 or $data['httpcode'] == 503 ) {  // retry
            Amazon_Retry::setData( $param );
            $listOrders = Amazon_Retry::getOrders();
        } else {
            $this->_error( $data );
        }

        return $listOrders;

    }

    /**
     * 抓取item信息
     *
     * @param: $option array API参数
     *
     * return array
     */
    public function getItems( $option ) {
        $this->_AWSAccessKeyId = $option['AWSAccessKeyId'];
        $this->_SellerId = $option['SellerId'];
        $this->_Key = $option['Key'];
        $this->_Url = $option['Server'] . 'Orders/' . self::SERVER_VERSION;
        unset($option['Server']);
    
        $param = $this->_getParam($option);

        $curl = new Amazon_Curl();
        $curl -> setParam($param);
        $data = $curl -> perform();

        $listItems = array();
        if( $data['httpcode'] == 200 ) {
            $listItems = $this->_getItemsDataFormat( $this->_getItemsData($data) );
        } else if ( $data['httpcode'] == 501 or $data['httpcode'] == 503 ) {
            Amazon_Retry::setData( $param );
            $listItems = Amazon_Retry::getItems();
        } else {
            $this->_error( $data );
        }

        return $listItems;
    }

    /**
     * 通过order的next token 抓取item信息
     *
     * @param: $nextToken hash
     *
     * return array
     */
    private function _getOrdersByNextToken( $nextToken ) {

        $option = [
                'AWSAccessKeyId' => $this->_AWSAccessKeyId,
                'Action' => 'ListOrdersByNextToken',
                'SellerId' => $this->_SellerId,
                'Key' => $this->_Key,
                'NextToken' => $nextToken
                ];

        $param = $this->_getParam($option);

        $curl = new Amazon_Curl();
        $curl -> setParam($param);
        $data = $curl -> perform();

        $listOrders = array();
        if( $data['httpcode'] == 200 ) {
            $listOrders = $this->_getOrdersByTokenData( $data );
        } else if ( $data['httpcode'] == 501 or $data['httpcode'] == 503 ) {  // retry
            Amazon_Retry::setData( $param );
            $listOrders = Amazon_Retry::getOrdersByNextToken();
        } else {
            $this->_error( $data );
        }

        return $listOrders;
    }

    /**
     * 通过item的next token 抓取item信息
     *
     * @param: $nextToken hash
     *
     * return array
     */
    private function _getItemsByNextToken( $nextToken ) {

        $option = [
                'AWSAccessKeyId' => $this->_AWSAccessKeyId,
                'Action' => 'ListOrderItemsByNextToken',
                'SellerId' => $this->_SellerId,
                'Key' => $this->_Key,
                'NextToken' => $nextToken
            ];

        $param = $this->_getParam($option);

        $curl = new Amazon_Curl();
        $curl -> setParam($param);
        $data = $curl -> perform();

        $listItems = array();
        if( $data['httpcode'] == 200 ) {
            $listItems = $this->_getItemsByTokenData( $data );
        } else if ( $data['httpcode'] == 501 or $data['httpcode'] == 503 ) {
            Amazone_Retry::setData( $param );
            $listItems = Amazon_Retry::getItemsByNextToken();
        } else {
            $this->_error( $data );
        }

        return $listItems;
    }

    /**
     * 获取order数据
     *
     * @param: $data array 抓取的数据
     *
     * return array
     */
    private function _getOrdersData( $data ) {
        $order = $this->_xml2Array( $data['data'] );

        $listOrders = array();
        if( isset($order['ListOrdersResult']) ) {
            if( isset($order['ListOrdersResult']['Orders']['Order']) ) {
                $listOrders = $order['ListOrdersResult']['Orders']['Order'];
            }

            // next token
            if( isset($order['ListOrdersResult']['NextToken']) ) {
                $token = $order['ListOrdersResult']['NextToken'];
                $listOrders = array_merge($listOrders, $this->_getOrdersByNextToken( $token ));
            }
        }

        return $listOrders;
    }

    /**
     * 获取item数据
     *
     * @param: $data array 抓取的数据
     *
     * return array
     */
    private function _getItemsData( $data ) {
        $item = $this->_xml2Array( $data['data'] );

        $listItems = array();
        if( isset($item['ListOrderItemsResult']) ) {
            if( isset($item['ListOrderItemsResult']['OrderItems']['OrderItem']) ) {
                $listItems = $item['ListOrderItemsResult']['OrderItems']['OrderItem'];
            }

            // next token
            if( isset($item['ListOrderItemsResult']['NextToken']) ) {
                $token = $item['ListOrderItemsResult']['NextToken'];
                $listItems = array_merge($listItems, $this->_getItemsByNextToken( $token ));
            }
        }

        return $listItems;
    }

    /**
     * 通过Amazon返回的next token抓取order数据
     *
     * @param: $data array 抓取的数据
     *
     * return array;
     */
    private function _getOrdersByTokenData( $data ) {
    
        $order = $this->_xml2Array( $data['data'] );

        $listOrders = array();
        if( isset($order['ListOrdersByNextTokenResult']['Orders']['Order']) ) {
            $listOrders = $order['ListOrdersByNextTokenResult']['Orders']['Order'];
        }

        // next token
        if( isset($order['ListOrdersByNextTokenResult']['NextToken']) ) {
            $token = $order['ListOrdersResult']['NextToken'];
            $listOrders = array_merge($listOrders, $this->_getOrdersByNextToken( $token ));
        }

        return $listOrders;

    }

    /**
     * 通过Amazon返回的next token抓取item数据
     *
     * @param: $data array 抓取的数据
     *
     * return array;
     */
    private function _getItemsByTokenData( $data ) {
        $item = $this->_xml2Array( $data['data'] );

        $listItems = array();
        if( isset($item['ListOrderItemsByNextTokenResult']['OrderItems']['OrderItem']) ) {
            $listItems = $item['ListOrderItemsByNextTokenResult']['OrderItems']['OrderItem'];
        }

        if( isset($item['ListOrderItemsByNextTokenResult']['NextToken']) ) {
            $token = $item['ListOrderItemsByNextTokenResult']['NextToken'];
            $listItems = array_merge($listItems, $this->_getItemsByNextToken( $token ));
        }
    
        return $listItems;
    }

    /**
     * Order数据转换成入库数组
     *
     * @param: $datas array 抓取的数据
     *
     * return array
     */ 
    private function _getOrdersDataFormat( $datas ) {

        $newDatas = array();
        if(isset($datas[0]) && is_array($datas[0])) {
            foreach ($datas as $data) {
                $newData = $this->_orderDataMap( $data );
                $newDatas[] = $newData;
            }
        } else if( !empty($datas) ){
            $newDatas[0] = $this->_orderDataMap( $datas );
        }

        return $newDatas;
    }

    /**
     * Item数据转换成入库数组
     *
     * @param: $datas array 抓取的数据
     *
     * return array
     */ 
    private function _getItemsDataFormat( $datas ) {

        $newDatas = array();
        if ( isset( $datas[0] ) && is_array($datas[0]) ) {
            foreach ($datas as $data) {
                $newData = $this->_itemDataMap( $data );
                $newDatas[] = $newData;
            }

        } else {
            $newDatas[0] = $this->_itemDataMap( $datas );
        }

        return $newDatas;
    }

    /**
     * order数据库字段映射
     *
     * @param: $data array 需映射的数据
     *
     * return array
     */
    private function _orderDataMap ($data) {
        $new_data = [
                'entry_id'                 => isset($data['AmazonOrderId']) ? $data['AmazonOrderId'] : '',
                'name'                     => isset($data['BuyerName']) ? $data['BuyerName'] : '',
                'email'                    => isset($data['BuyerEmail']) ? $data['BuyerEmail'] : '',
                'market_id'                => isset($data['MarketplaceId']) ? $data['MarketplaceId'] : '',
                'total'                    => isset($data['OrderTotal']['Amount']) ? $data['OrderTotal']['Amount'] : '',
                'currency'                 => isset($data['OrderTotal']['CurrencyCode']) ? $data['OrderTotal']['CurrencyCode'] : '',
                'shipping_name'            => isset($data['ShippingAddress']['Name']) ? $data['ShippingAddress']['Name'] : '',
                'shipping_phone'           => isset($data['ShippingAddress']['Phone']) ? $data['ShippingAddress']['Phone'] : '',
                'shipping_country'         => isset($data['ShippingAddress']['CountryCode']) ? $data['ShippingAddress']['CountryCode'] : '',
                'shipping_state_or_region' => isset($data['ShippingAddress']['StateOrRegion']) ? $data['ShippingAddress']['StateOrRegion'] : '',
                'shipping_city'            => isset($data['ShippingAddress']['City']) ? $data['ShippingAddress']['City'] : '',
                'shipping_address1'        => isset($data['ShippingAddress']['AddressLine1']) ? $data['ShippingAddress']['AddressLine1'] : '',
                'shipping_address2'        => isset($data['ShippingAddress']['AddressLine2']) ? $data['ShippingAddress']['AddressLine2'] : '',
                'shipping_address3'        => isset($data['ShippingAddress']['AddressLine3']) ? $data['ShippingAddress']['AddressLine3'] : '',
                'shipping_postal_code'     => isset($data['ShippingAddress']['PostalCode']) ? $data['ShippingAddress']['PostalCode'] : '',
                'ship_level'               => isset($data['ShipServiceLevel']) ? $data['ShipServiceLevel'] : '',
                'shipment_level'           => isset($data['ShipmentServiceLevelCategory']) ? $data['ShipmentServiceLevelCategory'] : '',
                'fulfillment'              => isset($data['FulfillmentChannel']) ? $data['FulfillmentChannel'] : '',
                'shipped_by_amazon_tfm'    => isset($data['ShippedByAmazonTFM']) ? $data['ShippedByAmazonTFM'] : '',
                'payment_method'           => isset($data['PaymentMethod']) ? $data['PaymentMethod'] : '',
                'from'                     => isset($data['SalesChannel']) ? $data['SalesChannel'] : '',
                'status'                   => isset($data['OrderStatus']) ? $data['OrderStatus'] : '',
                'order_status'             => PENDING_ORDER,
                'created_at'               => isset($data['PurchaseDate']) ? $data['PurchaseDate'] : '',
                ];

        return $new_data;
    }

    /**
     * item数据库字段映射
     *
     * @param: $data array 需映射的数据
     *
     * return array
     */
    private function _itemDataMap ($data) {
        $new_data = [
                'entry_id'          => isset($data['OrderItemId']) ? $data['OrderItemId'] : '',
                'name'              => isset($data['Title']) ? $data['Title'] : '',
                'sku'               => isset($data['SellerSKU']) ? $data['SellerSKU'] : '',
                'price'             => isset($data['ItemPrice']['Amount']) ? $data['ItemPrice']['Amount'] : '',
                'currency'          => isset($data['ItemPrice']['CurrencyCode']) ? $data['ItemPrice']['CurrencyCode'] : '',
                'quantity'          => isset($data['QuantityOrdered']) ? $data['QuantityOrdered'] : '',
                'shipping_price'    => isset($data['ShippingPrice']['Amount']) ? $data['ShippingPrice']['Amount'] : '',
                'shipping_currency' => isset($data['ShippingPrice']['CurrencyCode']) ? $data['ShippingPrice']['CurrencyCode'] : ''
                ];

        return $new_data;
    }

    /**
     * 获取API参数
     *
     * @param: $option array 参数
     *
     * return array 转换后的参数
     */
    private function _getParam( $option ) {
    
        $amazon = new Amazon();
        $amazon -> setData( $option, $this->_Url );
        $data = $amazon -> combine();

        $param = [
            'url' => $this->_Url, 
            'query' => $data,    
            ];
    
        return $param;
    }

    /**
     * 错误信息
     *
     * @param: $data array 返回数据
     *
     * return viod
     */
    private function _error( $data ) {
        $error = $this->_xml2Array($data['data'])['Error'];
        $errorInfo = '';
        foreach($error as $key => $value) {
            $errorInfo .= '[' . $key . ']:' . $value . "\n";
        }

        throw new Amazon_Exception($errorInfo);
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
