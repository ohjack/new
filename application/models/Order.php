<?php

class Order {

    const PENDING_ORDER   = 0; // 待处理
    const HAD_MATCH_ORDER = 1; // 已分配物流
    const PART_SEND_ORDER = 2; // 部分发货
    const ALL_SEND_ORDER  = 3; // 全部发货
    const MARK_SEND_ORDER = 4; // 先标记发货

    
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

        $order->order_status = Config::get('application.order_status')[$order->order_status];

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

            } else if (trim($option) != '') {
                $table = $table->where($key, '=', $option);
            }
        }

        // 排序
        $table = $table->order_by('orders.order_status', 'ASC')
                       ->order_by('orders.created_at', 'DESC');

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
     * 更新订单物流
     *
     * @param: $order_id integer 订单ID
     * @param: $logistics string 物流系统名称
     *
     * return void
     */
    public static function updateLogistics( $order_id, $logistics ) {

        $option = [ 'logistics' => $logistics , 'order_status' => self::HAD_MATCH_ORDER ];
    
        DB::table('orders')->where('id', '=', $order_id)->update( $option );
    }

    /**
     * 统计指定物流订单 
     *
     * @param: $logistics string 物流名称
     *
     * return integer
     */
    public static function countLogistics( $logistics ) {
        return DB::table('orders')->where('logistics', '=', $logistics)->count();
    }

    /**
     * 批量设置mark
     *
     * @param: $mark_ids  array 标识IDs
     * @param: $order_ids array 订单IDs
     *
     * return viod
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
     * @param: $user_id integer 用户ID
     *
     * return array
     */
    public static function getUnspiderOrders( $user_id ) {
        return DB::table('orders')->where('user_id', '=', $user_id)->where_null('crawled_at')->get(['id', 'entry_id', 'from']);
    }

    /**
     * 获取可以确定的订单
     *
     * @param: $user_id integer 用户ID
     * @param: $from    string  来源
     *
     * return array
     */
    public static function getShipOrders( $user_id, $from ) {

        $status = [ self::PART_SEND_ORDER, self::ALL_SEND_ORDER, self::MARK_SEND_ORDER ];
        $orders = DB::table('orders')->where('user_id', '=', $user_id)
                                     ->where('from' , '=', $from)
                                     ->where('confirm' , '=', 1)
                                     ->where_in('order_status', $status)
                                     ->get(['id', 'entry_id', 'order_status']);


        foreach($orders as $order) {
            $fields = [
                'items.entry_id', 'shipped.tracking_no', 'shipped.method', 'shipped.company',
                'shipped.quantity as shipped_quantity', 'items.id as item_id,'
                ];
            $order->items = DB::table('items')->left_join('shipped', 'items.id', '=', 'shipped.item_id')
                                              ->where('items.order_id', '=', $order->id)->get($fields);
        }

        return $orders;
    }

    /**
     * 标记为可确认订单
     *
     * @param: $ids array 订单IDs
     *
     * return void
     */
    public static function confirm( $ids ) {

        if(empty($ids)) return;

        $user_id = 1;
        $data = ['confirm' => 1];

        DB::table('orders')->where('user_id', '=', $user_id)
                           ->where('confirm', '=', 0)
                           ->where_in('order_status', [self::PART_SEND_ORDER, self::ALL_SEND_ORDER, self::MARK_SEND_ORDER])
                           ->where_in('id', $ids)
                           ->update( $data );
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
            'message' => [ 'total' => 0 ]
            ];

        // 遍历平台进行抓取
        foreach ($user_platforms as $user_platform) {

            // 实例化API
            $spider_name = 'Spider_Orders_' . $user_platform->type;
            $order_spider = new Spider_Orders( new $spider_name() );

            // 获取配置
            $base_option = array_merge(unserialize($user_platform->option), unserialize($user_platform->user_option));
            $option = $order_spider->getOrderOption( $user_platform->id, $base_option);

            if(empty($option)) continue; // 如果为空跳过抓取

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
                    $result['message']['total']++;
                    $order['user_id'] = $user_platform->user_id;
                    $order_id = static::saveOrder($order);
                }
            }

            // 更新抓取日志
            SpiderLog::updateLastSpider('order', $user_platform->id, $result['message']['total']);

        }

        return $result;
    }

    /**
     * 订单匹配物流
     *
     * @param: $user_id integer 用户ID
     *
     * return boolean
     */
    public static function Match( $user_id ) {

        // 简单物流匹配规则
        $rules = [
                'coolsystem'  => ['orders.shipping_country' => 'US', 'orders.from' => 'Amazon.com'],
                'birdsystem'  => ['orders.from' => 'Amazon.co.uk'],
                'micaosystem' => []
            ];

        $had_handle = [];
        foreach($rules as $logistics => $rule) {
            $table = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id');
            foreach ($rule as $field => $value) {
                $table = $table->where($field, '=', $value);
            }

            $items = $table->where('orders.user_id', '=', $user_id)
                           ->where('orders.order_status', '=', self::PENDING_ORDER)
                           ->get();

            foreach($items as $item) {
                $skumap_exsits = SkuMap::chkMap($item->sku, $logistics);
                if( $skumap_exsits && !in_array($item->order_id, $had_handle)) {
                    self::updateLogistics($item->order_id, $logistics);
                    $had_handle[] = $item->order_id;
                }
            }
        }

        return true;
    }

    /**
     * 确认订单
     *
     * @param: $user_platform array 用户平台数据
     *
     * return void
     */
    public static function confirmOrders( $user_platforms ) {

        foreach ($user_platforms as $user_platform) {

            $orders = Order::getShipOrders( $user_platform->user_id, $user_platform->name );

            $rsync_name = 'Rsync_Orders_' . $user_platform->type;
            $rsyncer = new Rsync_Orders( new $rsync_name );
            $options = array_merge(unserialize($user_platform->option), unserialize($user_platform->user_option));

            foreach ($orders as $order) {
                $data = $rsyncer->confirmOrders( $options, $order );
                if(!empty($data)) // 更新订单状态
                    DB::table('orders')->where('id', '=', $order->id)->update($data);
            }

        }
    }
}
?>
