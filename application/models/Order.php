<?php

class Order {
    
    /**
     * 获取订单
     *
     * @param: $order_id
     *
     * return array
     */
    public static function getOrder( $order_id ) {

        $order = DB::table('orders')->find( $order_id );
        $order->items = Item::getItems( $order_id );
        $order->marks = Mark::getByOrderId( $order_id );

        return $order;
    }

    /**
     * 获取订单列表
     *
     * @param: $per_page integer 每页记录数
     * @param: $option   array   搜索参数
     *
     * reutrn object
     */
    public static function getOrders( $per_page, $options ) {

        $fields = [
            'orders.id', 'orders.created_at', 'orders.entry_id', 'orders.currency',
            'orders.total', 'orders.shipment_level', 'orders.order_status', 'orders.shipping_name',
            'shipping_name', 'shipping_address1', 'shipping_address2', 'shipping_address3',
            'shipping_city', 'shipping_state_or_region', 'shipping_country', 'shipping_postal_code',
            'shipping_phone', 'payment_method', 'from'
             ];

        $table = DB::table('orders');

        // 处理条件
        foreach ($options as $key => $option) {
            if($key == 'mark_id') {
                $table = $table->left_join('orders_mark', 'orders.id', '=', 'orders_mark.order_id');
            }

            if(is_array($option)) {
                $table = $table->where_in($key, $option);

            } else if (trim($option)) {
                $table = $table->where($key, '=', $option);
            }
        }

        // 排序
        $table = $table->order_by('orders.id', 'DESC')
                       ->order_by('orders.order_status', 'ASC');

        $orders = $table->paginate( $per_page , $fields);

        // 整理列表需要的产品 标识等数据
        foreach ($orders->results as $order) {

            $fields = [ 'id', 'sku', 'entry_id', 'name', 'quantity' ];
            $items = DB::table('items')->where('order_id', '=', $order->id)->get( $fields );

            $order->items = $items;

            $order->marks = Mark::getByOrderId( $order->id );

        }

        return $orders;
    
    }

    /**
     * 保存订单数据
     *
     * @param: $data array 订单数据
     *
     * return integer 订单ID
     */
    public static function saveOrder( $data ) {
        return DB::table('orders')->insert_get_id( $data );
    } 

    /**
     * 更新订单数据
     *
     * @param: $order_id integer 订单ID
     * @param: $data array 需更新的订单数据
     *
     * return void
     */
    public static function updateOrder( $order_id, $data ) {
        DB::table('orders')->where('id', '=', $order_id)->update($data);
    }

    /**
     * 给订单分配物流
     *
     * @param: $order_id integer 订单ID
     * @param: $logistics string 物流系统名称
     *
     * return void
     */
    public static function setLogistics( $order_id, $logistics ) {

        $option = [ 'logistics' => $logistics , 'order_status' => 'matched' ];
    
        DB::table('orders')->where('id', '=', $order_id)->update( $option );
    }

    /**
     * 统计指定物流订单 
     *
     */
    public static function countLogistics( $logistics ) {
        return DB::table('orders')->where('logistics', '=', $logistics)->count();
    }

    /**
     * 批量设置mark
     */
    public static function setMarks($mark_ids, $order_ids) {
        DB::table('orders_mark')->where_in('order_id', $order_ids)->delete();

        foreach($mark_ids as $mark_id) {
            foreach($order_ids as $order_id) {
                $data = [ 'order_id' => $order_id, 'mark_id' => $mark_id ];
                DB::table('orders_mark')->insert($data);
            }
        }
    }

    /**
     * 根据第三方ID获取订单ID
     *
     * @param: $entry_id integer 第三方ID
     *
     * return integer
     */
    public static function getIdByEntryId( $entry_id ) {
        return DB::table('orders')->where('entry_id', '=', $entry_id)->only('id');
    }

    /**
     * 获取未抓取的订单
     *
     * return array 订单ID 第三方ID 来源
     */
    public static function getUnspiderOrders() {
        return DB::table('orders')->where_null('crawled_at')->get(['id', 'entry_id', 'from']);
    }

    /**
     * 获取可以确定的订单
     */
    public static function getShipOrders( $options ) {

        $table = DB::table('orders');

        // 处理条件
        foreach ($options as $key => $option) {
            if(is_array($option)) {
                $table = $table->where_in($key, $option);

            } else if (trim($option)) {
                $table = $table->where($key, '=', $option);
            }
        }

        $order_ids = $table->lists('id');

        return $order_ids;
    
    }


    /**
     * 确认订单
     */
    public static function confirmOrders( $user_platforms ) {

        foreach ($user_platforms as $user_platform) {

            // 得到用户平台下所有需要确认的订单
            $options = [
                'order_status' => [ 2, 3, 4 ],  // 2部分发货 3已发货 4先确定发货
                'from'         => $user_platform->name,
                'user_id'      => $user_platform->user_id
                ];

            $order_ids = Order::getShipOrders( $options );

            $rsync_name = 'Rsync_Orders_' . $user_platform->type;
            $rsyncer = new Rsync_Orders( new $rsync_name );
            $options = array_merge(unserialize($user_platform->option), unserialize($user_platform->user_option));
            foreach ($order_ids as $order_id) {
                $rs = $rsyncer->confirmOrders( $options, $order_id );
                print_r($rs);die;
            }

        }

        //return $rs;
        die;
    
    }

    /**
     * 抓取订单
     *
     * @param: $platforms array 用户销售平台
     *
     * return array 抓取结果
     */
    public static function spiderOrders( $user_platforms ) {

        // 初始化返回
        $result = [
            'status'  => 'success',
            'message' => [ 'total' => 0, 'insert' => 0, 'update' => 0 ]
            ];

        // 遍历平台进行抓取
        foreach ($user_platforms as $user_platform) {

            // 获取平台配置
            $platform_name = 'Platform_' . $user_platform->type;
            $platform = new Platform( new $platform_name() );
            $base_option = array_merge(unserialize($user_platform->option), unserialize($user_platform->user_option));
            $option = $platform->getOrderOption( $user_platform->id, $base_option );

            if(empty($option)) continue; // 如果为空跳过抓取

            // 实例化API
            $spider_name = 'Spider_Orders_' . $user_platform->type;
            $order_spider = new Spider_Orders( new $spider_name() );

            // 抓取订单
            try {
                $orders = $order_spider->getOrders($option);
            } catch (Amazon_Curl_Exception $e) {
                $result = [ 'status' => 'error', 'message' => 'Curl Error: ' . $e->getError() ];
                return $result;
            } catch (Amazon_Exception $e) {
                $result = [ 'status' => 'error', 'message' => 'Amazone API Error: ' . $e->getError() ];
                return $result;
            }

            // 订单入库
            foreach ($orders as $order) {
                $order_id = static::getIdByEntryId($order['entry_id']);
                if( empty($order_id) ) {
                    $result['message']['insert']++;
                    $order_id = static::saveOrder($order);
                } else {
                    $result['message']['update']++;
                    static::updateOrder($order_id, $order);
                }

                $result['message']['total']++;
            }

            // 更新抓取日志
            SpiderLog::updateLastSpider('order', $user_platform->id);

        }

        return $result;
    
    }
}
?>
