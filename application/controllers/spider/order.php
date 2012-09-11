<?php

class Spider_Order_Controller extends Base_Controller {
    
    public $restful = true;

    public function get_index() {

        $platforms = User::getPlatform(1);

        $result = ['status' => 'success'];

        foreach ($platforms as $platform) {
            $tmp_name = explode('.', $platform->name);
            $platform_name = $tmp_name[0];
            $spider = 'SpiderOrders_' . $platform_name;

            $option = array_merge(unserialize($platform->option), unserialize($platform->user_option));

            $mark = md5(implode(',', $option));

            $spider_log = SpiderLog::getLastSpider('order', $mark);

            if(empty($spider_log->lasttime)) {
                $lasttime = date('Y-m-d') . ' 00:00:00';
            } else {
                $lasttime = $spider_log->lasttime;

                // 如果小于两分钟直接跳过
                if(time() - strtotime($lasttime) < 120) {
                    continue;
                }
            }

            // 只抓取spider log记录的时间以后的订单
            $option['CreatedAfter'] = $lasttime;
            $option['OrderStatus.Status.1'] = 'Unshipped';
            $option['OrderStatus.Status.2'] = 'PartiallyShipped';

            $orderSpider = new SpiderOrders(new $spider());

            try {
                $orders = $orderSpider->getOrders($option);


                foreach ($orders as $order) {

                    $order_id  = DB::table('orders')->where('entry_id', '=', $order['entry_id'])->only('id');
                    if ( !$order_id ) {
                        $order_id = DB::table('orders')->insert_get_id($order);

                        $option = [
                            'AWSAccessKeyId' => $option['AWSAccessKeyId'],
                            'SellerId'       => $option['SellerId'],
                            'AmazonOrderId'  => $order['entry_id'],
                            'Key'            => $option['Key'],
                            'Interface'      => $option['Interface']
                            ];

                        try {
                            $items = $orderSpider->getItems( $option );

                            foreach ($items as $item) {
                                $item['order_id'] = $order_id;

                                $item_id = DB::table('items')->where('entry_id', '=', $item['entry_id'])->only('id');
                                if ( !$item_id ) {
                                    DB::table('items')->insert($item);
                                } else {
                                    DB::table('items')->where('id', '=', $item_id)->update($item);
                                }
                            }
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

            if( $result['status'] == 'success' ) {
                if( !empty($spider_log->id) )
                    SpiderLog::updateLastSpider( $spider_log->id );
                else
                    SpiderLog::insertLastSpider( 'order', $mark );
            }

        }


        
        /*
        $option = [
            'AWSAccessKeyId' => 'AKIAJGUMF5LENLIW6ZAQ',
            'SellerId' => 'A3LMXTNFZ71A3Q',
            'MarketplaceId.Id.1' => 'ATVPDKIKX0DER',
            'CreatedAfter' => '2012-08-30 00:00:00',
            'OrderStatus.Status.1' => 'Unshipped',
            'OrderStatus.Status.2' => 'PartiallyShipped',
            'Key' => 'jRa5CBIrZVTMm+GD9wwSNSQ+vwpyflw1eUn6aebL',
            ];
        */




        return Response::json($result);
    }
}
?>
