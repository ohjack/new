<?php

class Item {

    /**
     * 获取没有SKU映射的产品
     *
     * @param: $user_id integer 用户ID
     *  
     * return $items object
     */
    public static function getNoSkuItems( $user_id ) {

        $fields = [
            'items.sku',
            'items.name',
            'orders.shipping_country',
            'orders.from',
        ];


        $items = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id')
                                   ->where('orders.user_id', '=', $user_id)
                                   ->group_by('items.sku')
                                   ->group_by('orders.from')
                                   ->get($fields);

        // 按照简单规则匹配物流
        $items_unhandled = [];
        foreach ($items as $item) {
            if( $item->shipping_country == 'US' && $item->from == 'Amazon.com' )
                $item->logistics = 'coolsystem';
            elseif ($item->from == 'Amazon.co.uk')
                $item->logistics = 'birdsystem';
            else
                $item->logistics = 'micaosystem';

            if( !SkuMap::chkMap($item->sku, $item->logistics) ) {
                $items_unhandled[] = $item;
            }
        }

        return $items_unhandled;
    }

    /**
     * 获取产品
     *
     * param: $order_id integer 产品ID
     *
     * return array
     */
    public static function getItems( $order_id ) {
        return DB::table('items')->where('order_id', '=', $order_id)
                                 ->get();
    }

    /**
     * 保存产品
     *
     * @param: $data array 产品数据
     *
     * return void
     */
    public static function saveItem( $data ) {
        DB::table('items')->insert( $data );
    }

    /**
     * 通过entry id获取ID
     *
     * @param: $entry_id integer 第三方ID
     *
     * return integer 
     */
    public static function getIdByEntryId( $entry_id ) {
        return DB::table('items')->where('entry_id', '=', $entry_id)->only('id'); 
    }


    /**
     * 抓取产品
     *
     * @param $options array 用户销售平台
     *
     * return array 抓取结果
     */
    public static function spiderItems( $user_platforms ) {

        // 初始化返回
        $result = [
            'status'  => 'success',
            'message' => [ 'total' => 0 ]
            ];

        // 整理平台
        $platforms = [];
        foreach($user_platforms as $user_platform) {
            $platforms[$user_platform->name] = $user_platform;
            $user_id = $user_platform->user_id;
        }

        // 获取未抓取的订单
        $orders = Order::getUnspiderOrders( $user_id );

        // 遍历订单进行抓取
        foreach ($orders as $order) {

            // 获取订单指定的平台配置
            $user_platform = $platforms[$order->from];

            // 实例化API
            $spider_name = 'Spider_Orders_' . $user_platform->type;
            $item_spider = new Spider_Orders( new $spider_name() );
            // 获取配置
            $base_option = array_merge(unserialize($user_platform->option), unserialize($user_platform->user_option));
            $base_option['order_id'] = $order->entry_id;
            $option = $item_spider->getItemOption( $base_option );

            if(empty($option)) continue; // 如果为空跳过抓取

            // 抓取产品
            try {
                $items = $item_spider->getItems( $option );
            } catch (Amazon_Curl_Exception $e) {
                $result = [ 'status' => 'error', 'message' => 'Curl Error: ' . $e->getError() ];
                return $result;
            } catch (Amazon_Exception $e) {
                $result = [ 'status' => 'error', 'message' => 'Amazone API Error: ' . $e->getError() ];
                return $result;
            }

            // 产品入库
            foreach ($items as $item) {
                $item_id = static::getIdByEntryId( $item['entry_id'] );
                if( !$item_id ) {
                    $result['message']['total']++;
                    $item['order_id'] = $order->id;
                    static::saveItem( $item );
                }
            }

            // 标记订单已经抓取状态
            DB::table('orders')->where('id', '=', $order->id)->update(['crawled_at' => date('Y-m-d H:i:s')]);

        }

        // 如果没有需要处理的SKU匹配一次物流
        if(!count(Item::getNoSkuItems($user_id))) {
            Order::Match($user_id);
        }

        return $result;
    }
}

?>
