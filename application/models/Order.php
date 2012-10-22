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

        $order->order_status = Config::get('application.order_status')[$order->order_status]['desc'];

        $track=Track::getTracking($order_id);

        if(!empty($track[0]->status))
            $track[0]->status = Config::get('application.track_status')[$track[0]->status];
            
        $order->tracking=$track;
        
        return $order;
    }

    /**
     * ajax 获取订单列表
     *
     * @param: $user_id integer 用户ID
     * @param: $options array   筛选条件
     *
     * return object
     */
    public static function ajaxOrders( $user_id, $table_fields = [], $options = [] ) {

        $table_fields = empty($table_fields) ? Config::get('order_list_fields') : $table_fields;

        $fields = [];
        foreach($table_fields as $key => $field) {
            if(is_array($field['field'])) {
                $fields[] = DB::raw('CONCAT (' . implode(',\' \',', $field['field']) . ') as ' . $key);
            } else {
                $fields[] = $field['field'] . ' as ' . $key;
            }
        }

        /*
        // 系统设置字段
        $fields = [
            'order_id'       => ['name'=>'', 'field' => 'orders.id'],
            'order_entry_id' => ['name'=>'订单ID', 'field' => 'orders.entry_id'],
            'purchased_at'   => ['name'=>'购买时间', 'field' => 'orders.created_at'],
            'order_total'    => ['name'=>'订单金额', 'field' => ['orders.currency', 'orders.total']],
            'shipment_level' => ['name'=>'配送等级', 'field'=> 'orders.shipment_level'],
            'order_status'   => ['name'=>'订单状态', 'field'=> 'orders.order_status'],
            'shipping_name'  => ['name'=>'收货人', 'field'=> 'orders.shipping_name'],
            'shipping_address'  => ['name'=>'发货地址', 'field'=> ['orders.shipping_address3', 'orders.shipping_address2', 'orders.shipping_address1']],
            'shipping_city'  => ['name' => '发货城市', 'field'=>'orders.shipping_city'],
            'shipping_state_or_region'  => ['name' => '发货州/区', 'field'=>'orders.shipping_state_or_region'],
            'shipping_country'  => ['name' => '发货国家', 'field'=>'orders.shipping_country'],
            'shipping_postal_code'  => ['name' => '邮编', 'field'=>'orders.shipping_postal_code'],
            'shipping_phone'  => ['name' => '电话', 'field'=>'orders.shipping_phone'],
            'from'  => ['name' => '来源', 'field'=>'orders.from'],
            ];
        echo serialize($fields);
        die;
         */

        $table = DB::table('orders')->select($fields)
                                    ->where('orders.user_id', '=', $user_id);

        if($options) {
            foreach ($options as $key => $value) {
                $table = $table->where($key, '=', $value);
            }
        }

        $table = $table->order_by('orders.order_status', 'ASC')
                       ->order_by('orders.created_at', 'DESC');

        return $table;
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
        $user_id=Sentry::user()->get('id');
        $fields = [
            'orders.id', 'orders.created_at', 'orders.entry_id', 'orders.currency',
            'orders.total', 'orders.shipment_level', 'orders.order_status', 'orders.shipping_name',
            'shipping_name', 'shipping_address1', 'shipping_address2', 'shipping_address3',
            'shipping_city', 'shipping_state_or_region', 'shipping_country', 'shipping_postal_code',
            'shipping_phone', 'payment_method', 'from'
             ];

        $table = DB::table('orders')->select($fields);

        // 处理条件
        foreach ($options as $key => $option) {
            if($key == 'mark_id') {
                $table = $table->left_join('orders_mark', 'orders.id', '=', 'orders_mark.order_id')
                                ->where('orders.user_id','=',$user_id);
            }

            if(is_array($option)) {
                $table = $table->where_in($key, $option)
                                ->where('orders.user_id','=',$user_id);

            } else if (trim($option) != '') {
                $table = $table->where($key, '=', $option)
                                ->where('orders.user_id','=',$user_id);
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

        $option = [ 'logistics' => $logistics , 'order_status' => HAD_MATCH_ORDER ];
    
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

        $orders =  DB::table('orders')->where('user_id', '=', $user_id)
                                      ->where_null('crawled_at')
                                      ->get(['id', 'entry_id', 'from']);

        return $orders;
    }

    /**
     * 统计需要录入物流信息订单
     */
    public static function totalInputLogistic( $user_id ) {
        $total = DB::table('orders')->where('user_id', '=', $user_id)
                                    ->where('order_status', '=', HAD_MATCH_ORDER)
                                    ->where('logistics', '!=', '')
                                    ->count();

        return $total;
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

        $status = [ PART_SEND_ORDER, ALL_SEND_ORDER, MARK_SEND_ORDER ];
        $fields = [
            'orders.id', 'orders.entry_id', 'order_status',
            'shipped.company', 'shipped.tracking_no', 'shipped.method'
            ];
        $orders = DB::table('orders')->left_join('shipped', 'orders.id', '=', 'shipped.order_id')
                                     ->where('user_id', '=', $user_id)
                                     ->where('from' , '=', $from)
                                     ->where_in('order_status', $status)
                                     ->get($fields);


        // 产品发货信息
        foreach($orders as $order) {
            $fields = [
                'items.entry_id', 'quantity', 'items.id as item_id,'
                ];
            $order->items = DB::table('items')->where('order_id', '=', $order->id)->get($fields);
        }

        return $orders;
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
            'message' => [ 'total' => 0, 'new' => 0, 'rsync' => 0 ]
            ];

        // 遍历平台进行抓取
        foreach ($user_platforms as $user_platform) {

            // 实例化API
            $spider_name = 'Spider_Orders_' . $user_platform->type;
            $order_spider = new Spider_Orders( new $spider_name() );

            // 获取配置
            $base_option = array_merge(unserialize($user_platform->option), unserialize($user_platform->user_option));

            // 同步取消订单option
            $option = $order_spider->getRsyncOption( $user_platform->id, $base_option );

            if(empty($option)) continue; // 如果为空跳过抓取

            if($option) {
                $orders = $order_spider->getOrders($option);
                if($orders) {
                    foreach( $orders as $order) {
                        $data = [
                            'order_status' => CHANNEL_ORDER,
                            ];
                        $order_id = static::getIdByEntryId($order['entry_id']);
                        if($order_id) {
                            static::updateOrder( $order_id, $data);
                        }
                        $result['message']['rsync']++;
                    }
                }
            }

            // 抓取订单option
            $option = $order_spider->getOrderOption( $user_platform->id, $base_option );
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
                $result['message']['total']++;
                if( empty($order_id) ) {
                    $result['message']['new']++;
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
                           ->where('orders.order_status', '=', PENDING_ORDER)
                           ->get();

            foreach($items as $item) {
                $skumap_exsits = SkuMap::chkMap($item->sku, $logistics);
                if( $skumap_exsits && !in_array($item->order_id, $had_handle)) {
                    Order::updateLogistics($item->order_id, $logistics);
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
