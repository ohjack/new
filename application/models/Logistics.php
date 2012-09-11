<?php

class Logistics {

    /**
     * 所有产品匹配物流
     *
     * 将所有未处理的的订单通过产品sku进行物流匹配操作
     *
     * return void
     *
     */
    public static function allHandle () {

        $rules = [
                'coolsystem' => ['orders.shipping_country' => 'US'],
                'birdsystem' => []
            ];

        $handled = array();
        foreach ($rules as $system => $rule) {

            // get items in the rule
            $obj = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id');
            foreach ($rule as $key => $value) {
                $obj = $obj->where($key, '=', $value);
            }
            $obj->where('orders.order_status', '=', 'unhandle');
            $items = $obj->get();

            // put the order to system
            foreach ($items as $item) {
                $exsits = SkuMap::chkMap($item->sku, $system);
                if( $exsits && !in_array($item->order_id, $handled) ) {
                    Order::setLogistics($item->order_id, $system);
                    $handled[] = $item->order_id;
                }
            }

        }
    }

    /**
     * 将所有的订单匹配给其他物流
     *
     * return void
     */
    public static function allOther() {

        $option = [
                'order_status' => 'other'
            ];

        DB::table('orders')->where('order_status', '=', 'unhandle')
                           ->update( $option );
    
    }

    /**
     * 将选中的订单匹配给其他物流
     *
     * return void
     */
    public static function listToOther($order_ids) {

        $option = [
                'order_status' => 'other'
            ];

        DB::table('orders')->where_in('id', $order_ids)
                           ->update( $option );
    }

    /**
     * 导出已经匹配好物流的订单
     *
     * return void
     */
    public static function getCSV($system) {

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $system . date('_Y_m_d') . '.csv');
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');

        $first_row = [
                'coolsystem' => '订单备注;Sellerrecord,下单时间,Ebay账户名,交易号(交易号相同订单，自动合并),EbayitemNo(为空自动生成),物品SKU,物品名称,数量,销售单价,校验码（绝对不能重复，否则无法导入订单）,运费,交易手续费,总计;币种,买家ID,收件人,地址1,地址2,state(必须为2位字母),city,邮编,国家,Coutrycode（为空默认US）,电话,E-mail',
                'birdsystem' => 'order-id,order-item-id,purchase-date,payments-date,reporting-date,promise-date,days-past-promise,buyer-email,buyer-name,buyer-phone-number,sku,product-name,quantity-purchased,quantity-shipped,quantity-to-ship,ship-service-level,recipient-name,ship-address-1,ship-address-2,ship-address-3,ship-city,ship-state,ship-postal-code,ship-country,sales-channel',
                'other' => '',
            ];

        $fields = [
            'coolsystem' => [
                'orders.entry_id as order_id', 
                'sku_map.target_sku as sku', 
                'items.quantity', 
                'orders.shipping_name', 
                'orders.shipping_address1', 
                'orders.shipping_address2', 
                'orders.shipping_address3',
                'orders.shipping_state_or_region',
                'orders.shipping_city',
                'orders.shipping_postal_code',
                'orders.shipping_country',
                'orders.shipping_phone'
                ],
            'birdsystem' => [
                'orders.entry_id as order_id',
                'items.entry_id as item_id',
                'orders.created_at',
                'orders.shipment_level',
                'orders.email',
                'orders.name',
                'orders.shipping_phone as phone',
                'sku_map.target_sku as sku',
                'items.name as product_name',
                'items.quantity',
                'orders.shipment_level',
                'orders.shipping_name',
                'orders.shipping_address1',
                'orders.shipping_address2',
                'orders.shipping_address3',
                'orders.shipping_city',
                'orders.shipping_state_or_region',
                'orders.shipping_postal_code',
                'orders.shipping_country',
                'orders.from'
                ]
            ];

        echo $first_row[$system] . "\n";

        $items = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id')
                                   ->left_join('sku_map', 'items.sku', '=', 'sku_map.original_sku')
                                   ->where('orders.order_status', '=', $system)
                                   ->get($fields[$system]);

        if($system == 'coolsystem') {
            foreach ($items as $item) {
                $address2 = trim("{$item->shipping_address3} {$item->shipping_address2}");
                echo ",,,,{$item->order_id},," . 
                     "{$item->sku},," . 
                     "{$item->quantity},,,,,,," . 
                     "{$item->order_id}," . 
                     "{$item->shipping_name}," . 
                     "\"{$item->shipping_address1}\"," . 
                     "{$address2}," . 
                     "\"{$item->shipping_state_or_region}\"," . 
                     "\"{$item->shipping_city}\"," . 
                     "{$item->shipping_postal_code},," . 
                     "{$item->shipping_country}," . 
                     "{$item->shipping_phone},\n";
            }
        } else if ($system == 'birdsystem') {
            foreach($items as $item) {
                $time = new DateTime($item->created_at);
                $time = $time->format( DateTime::ISO8601 );
                echo "{$item->order_id}," .
                     "{$item->item_id}," .
                     "{$time}," .
                     "{$time}," .
                     "{$time}," .
                     "{$time},," .
                     "{$item->email}," .
                     "{$item->name}," .
                     "{$item->phone}," .
                     "{$item->sku}," .
                     "\"{$item->product_name}\"," .
                     "{$item->quantity}," .
                     "0," .
                     "{$item->quantity}," .
                     "{$item->shipment_level}," .
                     "{$item->shipping_name}," .
                     "\"{$item->shipping_address1}\"," .
                     "\"{$item->shipping_address2}\"," .
                     "\"{$item->shipping_address3}\"," .
                     "\"{$item->shipping_city}\"," .
                     "\"{$item->shipping_state_or_region}\"," .
                     "{$item->shipping_postal_code}," .
                     "\"{$item->shipping_country}\"," .
                     "{$item->from}\n"; 
            }
        }
    
    }

}
?>
