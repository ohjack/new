<?php
/**
 * Amazon Order
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:AmazonOrder.php  2012年08月28日 星期二 10时58分15秒Z $
 */

class SpiderOrders_Amazon {

    private $_AWSAccessKeyId;
    private $_SellerId;
    private $_Key;

    const URL = 'https://mws.amazonservices.com/Orders/2011-01-01';

    public function getOrders( $option ) {

        $this->_AWSAccessKeyId = $option['AWSAccessKeyId'];
        $this->_SellerId = $option['SellerId'];
        $this->_Key = $option['Key'];

        $option['Action'] =  'ListOrders';

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
    
        $option['Action'] = 'ListOrderItems';
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
            if( isset($order['ListOrdersResult']['Orders']) ) {
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
            if( isset($item['ListOrderItemsResult']['OrderItems']) ) {
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
        if( isset($order['ListOrdersByNextTokenResult']) ) {
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
        if( isset($item['ListOrderItemsByNextTokenResult']) ) {
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
        foreach ($datas as $data) {
            $newData = [
                'entry_id'                 => $data['AmazonOrderId'],
                'name'                     => $data['BuyerName'],
                'email'                    => $data['BuyerEmail'],
                'market_id'                => $data['MarketplaceId'],
                'total'                    => $data['OrderTotal']['Amount'],
                'currency'                 => $data['OrderTotal']['CurrencyCode'],
                'shipping_name'            => $data['ShippingAddress']['Name'],
                'shipping_phone'           => $data['ShippingAddress']['Phone'],
                'shipping_country'         => $data['ShippingAddress']['CountryCode'],
                'shipping_state_or_region' => $data['ShippingAddress']['StateOrRegion'],
                'shipping_city'            => $data['ShippingAddress']['City'],
                'shipping_address1'        => isset($data['ShippingAddress']['AddressLine1']) ? $data['ShippingAddress']['AddressLine1'] : '',
                'shipping_address2'        => isset($data['ShippingAddress']['AddressLine2']) ? $data['ShippingAddress']['AddressLine2'] : '',
                'shipping_address3'        => isset($data['ShippingAddress']['AddressLine3']) ? $data['ShippingAddress']['AddressLine3'] : '',
                'shipping_postal_code'     => $data['ShippingAddress']['PostalCode'],
                'ship_level'               => $data['ShipServiceLevel'],
                'shipment_level'           => $data['ShipmentServiceLevelCategory'],
                'fulfillment'              => $data['FulfillmentChannel'],
                'shipped_by_amazon_tfm'    => $data['ShippedByAmazonTFM'] ? 1 : 0,
                'payment_method'           => $data['PaymentMethod'],
                'from'                     => $data['SalesChannel'],
                'status'                   => $data['OrderStatus'],
                'created_at'               => $data['PurchaseDate'],
                ];

            $newDatas[] = $newData;
        }

        return $newDatas;
        
    }

    private function _getItemsDataFormat( $datas ) {
        //$newDatas = array();
        //if ( count($datas) == 1 ) {
            $newDatas = [
                'entry_id'          => $datas['OrderItemId'],
                'name'              => $datas['Title'],
                'sku'               => $datas['SellerSKU'],
                'price'             => $datas['ItemPrice']['Amount'],
                'currency'          => $datas['ItemPrice']['CurrencyCode'],
                'quantity'          => $datas['QuantityOrdered'],
                'shipping_price'    => $datas['ShippingPrice']['Amount'],
                'shipping_currency' => $datas['ShippingPrice']['CurrencyCode']
                ];
        /*
        } else {
            foreach ($datas as $data) {
                $newData = [
                    'entry_id'          => $data['OrderItemId'],
                    'name'              => $data['Title'],
                    'sku'               => $data['SellerSKU'],
                    'price'             => $data['ItemPrice']['Amount'],
                    'currency'          => $data['ItemPrice']['CurrencyCode'],
                    'quantity'          => $data['QuantityOrdered'],
                    'shipping_price'    => $data['ShippingPrice']['Amount'],
                    'shipping_currency' => $data['ShippingPrice']['CurrencyCode']
                    ];

                $newDatas[] = $newData;
            }
        }
        */
        return $newDatas;
    }

    // get curl param
    private function _getParam( $option ) {
    
        $amazon = new Amazon();
        $amazon -> setData( $option, self::URL );
        $data = $amazon -> combine();

        $param = [
            'url' => self::URL, 
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
