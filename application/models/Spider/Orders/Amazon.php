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

    public function getOrders( $option ) {


        $this->_AWSAccessKeyId = $option['AWSAccessKeyId'];
        $this->_SellerId = $option['SellerId'];
        $this->_Key = $option['Key'];
        $this->_Url = $option['Server'] . 'Orders/2011-01-01';
        unset($option['Server']);

        $param = $this->_getParam($option);

        $curl = new Amazon_Curl();
        $curl -> setParam($param);
        $data = $curl -> perform();

        $listOrders = array();
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

    public function getItems( $option ) {
        $this->_AWSAccessKeyId = $option['AWSAccessKeyId'];
        $this->_SellerId = $option['SellerId'];
        $this->_Key = $option['Key'];
        $this->_Url = $option['Server'] . 'Orders/2011-01-01';
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


    // foramt orders data
    private function _getOrdersDataFormat( $datas ) {
        $newDatas = array();
        if(isset($datas[0]) && is_array($datas[0])) {
            foreach ($datas as $data) {

                $newData = [
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
                    'order_status'             => 'unhandle',
                    'created_at'               => isset($data['PurchaseDate']) ? $data['PurchaseDate'] : '',
                    ];

                $newDatas[] = $newData;
            }
        } else if( !empty($datas) ){

          $newDatas[0] = [
                    'entry_id'                 => isset($datas['AmazonOrderId']) ? $datas['AmazonOrderId'] : '',
                    'name'                     => isset($datas['BuyerName']) ? $datas['BuyerName'] : '',
                    'email'                    => isset($datas['BuyerEmail']) ? $datas['BuyerEmail'] : '',
                    'market_id'                => isset($datas['MarketplaceId']) ? $datas['MarketplaceId'] : '',
                    'total'                    => isset($datas['OrderTotal']['Amount']) ? $datas['OrderTotal']['Amount'] : '',
                    'currency'                 => isset($datas['OrderTotal']['CurrencyCode']) ? $datas['OrderTotal']['CurrencyCode'] : '',
                    'shipping_name'            => isset($datas['ShippingAddress']['Name']) ? $datas['ShippingAddress']['Name'] : '',
                    'shipping_phone'           => isset($datas['ShippingAddress']['Phone']) ? $datas['ShippingAddress']['Phone'] : '',
                    'shipping_country'         => isset($datas['ShippingAddress']['CountryCode']) ? $datas['ShippingAddress']['CountryCode'] : '',
                    'shipping_state_or_region' => isset($datas['ShippingAddress']['StateOrRegion']) ? $datas['ShippingAddress']['StateOrRegion'] : '',
                    'shipping_city'            => isset($datas['ShippingAddress']['City']) ? $datas['ShippingAddress']['City'] : '',
                    'shipping_address1'        => isset($datas['ShippingAddress']['AddressLine1']) ? $datas['ShippingAddress']['AddressLine1'] : '',
                    'shipping_address2'        => isset($datas['ShippingAddress']['AddressLine2']) ? $datas['ShippingAddress']['AddressLine2'] : '',
                    'shipping_address3'        => isset($datas['ShippingAddress']['AddressLine3']) ? $datas['ShippingAddress']['AddressLine3'] : '',
                    'shipping_postal_code'     => isset($datas['ShippingAddress']['PostalCode']) ? $datas['ShippingAddress']['PostalCode'] : '',
                    'ship_level'               => isset($datas['ShipServiceLevel']) ? $datas['ShipServiceLevel'] : '',
                    'shipment_level'           => isset($datas['ShipmentServiceLevelCategory']) ? $datas['ShipmentServiceLevelCategory'] : '',
                    'fulfillment'              => isset($datas['FulfillmentChannel']) ? $datas['FulfillmentChannel'] : '',
                    'shipped_by_amazon_tfm'    => isset($datas['ShippedByAmazonTFM']) ? $datas['ShippedByAmazonTFM'] : '',
                    'payment_method'           => isset($datas['PaymentMethod']) ? $datas['PaymentMethod'] : '',
                    'from'                     => isset($datas['SalesChannel']) ? $datas['SalesChannel'] : '',
                    'status'                   => isset($datas['OrderStatus']) ? $datas['OrderStatus'] : '',
                    'order_status'             => 'unhandle',
                    'created_at'               => isset($datas['PurchaseDate']) ? $datas['PurchaseDate'] : '',
                    ];
        
        }

        return $newDatas;
        
    }

    private function _getItemsDataFormat( $datas ) {
        $newDatas = array();
        if ( isset( $datas[0] ) && is_array($datas[0]) ) {
            foreach ($datas as $data) {
                $newData = [
                    'entry_id'          => isset($data['OrderItemId']) ? $data['OrderItemId'] : '',
                    'name'              => isset($data['Title']) ? $data['Title'] : '',
                    'sku'               => isset($data['SellerSKU']) ? $data['SellerSKU'] : '',
                    'price'             => isset($data['ItemPrice']['Amount']) ? $data['ItemPrice']['Amount'] : '',
                    'currency'          => isset($data['ItemPrice']['CurrencyCode']) ? $data['ItemPrice']['CurrencyCode'] : '',
                    'quantity'          => isset($data['QuantityOrdered']) ? $data['QuantityOrdered'] : '',
                    'shipping_price'    => isset($data['ShippingPrice']['Amount']) ? $data['ShippingPrice']['Amount'] : '',
                    'shipping_currency' => isset($data['ShippingPrice']['CurrencyCode']) ? $data['ShippingPrice']['CurrencyCode'] : ''
                    ];

                $newDatas[] = $newData;
            }

        } else {
            $newDatas[0] = [
                    'entry_id'          => isset($datas['OrderItemId']) ? $datas['OrderItemId'] : '',
                    'name'              => isset($datas['Title']) ? $datas['Title'] : '',
                    'sku'               => isset($datas['SellerSKU']) ? $datas['SellerSKU'] : '',
                    'price'             => isset($datas['ItemPrice']['Amount']) ? $datas['ItemPrice']['Amount'] : '',
                    'currency'          => isset($datas['ItemPrice']['CurrencyCode']) ? $datas['ItemPrice']['CurrencyCode'] : '',
                    'quantity'          => isset($datas['QuantityOrdered']) ? $datas['QuantityOrdered'] : '',
                    'shipping_price'    => isset($datas['ShippingPrice']['Amount']) ? $datas['ShippingPrice']['Amount'] : '',
                    'shipping_currency' => isset($datas['ShippingPrice']['CurrencyCode']) ? $datas['ShippingPrice']['CurrencyCode'] : ''
                ];
        }
        return $newDatas;
    }

    // get curl param
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

    private function _error( $data ) {
        $error = $this->_xml2Array($data['data'])['Error'];
        $errorInfo = '';
        foreach($error as $key => $value) {
            $errorInfo .= '[' . $key . ']:' . $value . "\n";
        }

        throw new Amazon_Exception($errorInfo);
    }

    private function _xml2Array( $xml ) {
        return json_decode(json_encode((array) simplexml_load_string( $xml )), 1);
    }

}

?>
