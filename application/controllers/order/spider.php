<?php

class Order_Spider_Controller extends Base_Controller {
    
    public $restful = true;

    public function get_index() {

        $orderSpider = new SpiderOrders(new SpiderOrders_Amazon());
        
        $option = [
            'AWSAccessKeyId' => 'AKIAJGUMF5LENLIW6ZAQ',
            'SellerId' => 'A3LMXTNFZ71A3Q',
            'MarketplaceId.Id.1' => 'ATVPDKIKX0DER',
            'CreatedAfter' => '2012-08-30 00:00:00',
            'OrderStatus.Status.1' => 'Unshipped',
            'OrderStatus.Status.2' => 'PartiallyShipped',
            'Key' => 'jRa5CBIrZVTMm+GD9wwSNSQ+vwpyflw1eUn6aebL',
            ];

        $result = [
            'status' => 'success'
            ];

        try {
            $orders = $orderSpider->getOrders($option);
        
            foreach ($orders as $order) {

                $order_id  = DB::table('orders')->where('entry_id', '=', $order['entry_id'])->only('id');
                if ( !$order_id ) {
                    $order_id = DB::table('orders')->insert_get_id($order);

                    $option = [
                        'AWSAccessKeyId' => 'AKIAJGUMF5LENLIW6ZAQ',
                        'SellerId' => 'A3LMXTNFZ71A3Q',
                        'AmazonOrderId' => $order['entry_id'],
                        'Key' => 'jRa5CBIrZVTMm+GD9wwSNSQ+vwpyflw1eUn6aebL'
                        ];

                    try {
                        $item = $orderSpider->getItems( $option );

                        //foreach ($items as $item) {
                            $item['order_id'] = $order_id;

                            $item_id = DB::table('items')->where('entry_id', '=', $item['entry_id'])->only('id');
                            if ( !$item_id ) {
                                DB::table('items')->insert($item);
                            } else {
                                DB::table('items')->where('id', '=', $item_id)->update($item);
                            }
                        //}
                    } catch (Amazon_Curl_Exception $e) {

                        $result = [
                            'status' => 'error', 
                            'message' => $e->getError()
                            ];
                    
                    } catch (Amazon_Exception $e) {

                        $result = [
                            'status' => 'error',
                            'message' => $e->getError()
                            ];
                    
                    }

                } else { // update
                    unset($order['order_status']);
                    DB::table('orders')->where('id', '=', $order_id)->update($order);
                }
                
            }

        } catch (Amazon_Curl_Exception $e) {
            // log
            $result = [
                'status' => 'error', 
                'message' => $e->getError()
                ];
        
        } catch (Amazon_Exception $e) {

            // log
            $result = [
                'status' => 'error',
                'message' => $e->getError()
                ];
        }

        return Response::json($result);
    }
}
?>
