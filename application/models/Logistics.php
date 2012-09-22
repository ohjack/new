<?php

class Logistics {

    /**
     * 所有产品匹配物流
     *
     * 将所有未处理的的订单通过产品sku进行物流匹配操作
     *
     * return array 匹配结果
     *
     */
    public static function allHandle () {

        $result = [
            'status'  => 'success',
            'message' => ['total' => 0]
            ];

        $rules = [
                'coolsystem'  => ['orders.shipping_country' => 'US', 'orders.from' => 'Amazon.com'],
                'birdsystem'  => ['orders.from' => 'Amazon.co.uk'],
                'micaosystem' => []
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
                    $result['message']['total']++;
                }
            }
        }

        return $result;
    }

    /**
     * 统计两物流的数量
     *
     *
     */
    public static function getTotal() {
    
        $systems = ['coolsystem', 'birdsystem'];
        return DB::table('orders')->where_in('logistics', $systems)
                                  ->where('order_status', '=', 'matched')
                                  ->count();
    }

    /**
     * 生成物流导入文件
     *
     * @param: $systems array 系统 暂时是酷&鸟系统
     *
     * return array
     */
    public static function getCsvFile( $systems ) {

        header('content-type:text/html;charset=utf-8');

        $first_row = [
            'coolsystem' => [
                '订单备注','Sellerrecord','下单时间','Ebay账户名','交易号(交易号相同订单，自动合并)',
                'EbayitemNo(为空自动生成)','物品SKU','物品名称','数量','销售单价',
                '校验码（绝对不能重复，否则无法导入订单）','运费','交易手续费','总计','币种','买家ID',
                '收件人','地址1','地址2','state(必须为2位字母)','city','邮编',
                '国家','Coutrycode（为空默认US）','电话','E-mail'
                ],
            'birdsystem' => [
                'order-id','order-item-id','purchase-date','payments-date','reporting-date',
                'promise-date','days-past-promise','buyer-email','buyer-name','buyer-phone-number',
                'sku','product-name','quantity-purchased','quantity-shipped','quantity-to-ship',
                'ship-service-level','recipient-name','ship-address-1','ship-address-2','ship-address-3',
                'ship-city','ship-state','ship-postal-code','ship-country','sales-channel'
                ],
            ];

        $fields = [
            'coolsystem' => [
                'orders.id', 'orders.entry_id as order_id', 'sku_map.target_sku as sku', 'items.quantity', 
                'orders.shipping_name', 'items.entry_id as item_id', 'orders.shipping_address1', 
                'orders.shipping_address2', 'orders.shipping_address3', 'orders.shipping_state_or_region', 
                'orders.shipping_city', 'orders.shipping_postal_code', 'orders.shipping_country', 
                'orders.shipping_phone'
                ],
            'birdsystem' => [
                'orders.id', 'orders.entry_id as order_id', 'items.entry_id as item_id',
                'orders.created_at', 'orders.shipment_level', 'orders.email',
                'orders.name', 'orders.shipping_phone as phone', 'sku_map.target_sku as sku',
                'items.name as product_name', 'items.quantity', 'orders.shipment_level',
                'orders.shipping_name', 'orders.shipping_address1', 'orders.shipping_address2',
                'orders.shipping_address3', 'orders.shipping_city', 'orders.shipping_state_or_region',
                'orders.shipping_postal_code', 'orders.shipping_country', 'orders.from'
                ]
            ];

        $result = [];
        $objPHPExcel = new PHPExcel();
        foreach($systems as $system) {
            $items = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id')
                                       ->left_join('sku_map', 'items.sku', '=', 'sku_map.original_sku')
                                       ->where('orders.logistics', '=', $system)
                                       ->where('orders.order_status', '=', 'matched')
                                       ->where('sku_map.logistics', '=', $system)
                                       ->get($fields[$system]);

            if( $items ) {
                $filename = sprintf('%s_%s_%s.xlsx', $system, 1, date('Y_m_d_H_i_s'));
                $filepath = path('public') . 'data' . DS . 'logistics_file' . DS . $filename;
                //$fp = fopen($filepath, 'w+') or die();
                //fputcsv($fp, $first_row[$system], "\t");
                $objPHPExcel->setActiveSheetIndex(0);

                $i = 0;
                foreach ($first_row[$system] as $row) {
                    $i++;
                    $cell = static::_autoCell($i) . '1';
                    $objPHPExcel->getActiveSheet()->SetCellValue($cell, $row);
                }
                $order_ids = [];
                $i = 1;
                foreach ($items as $item) {
                    $i++;
                    //$row = [];
                    if($system == 'coolsystem') {
                        $rows = [
                            '', '', '', '', $item->order_id, '',
                            $item->sku, '', $item->quantity, '', $item->item_id, '', '', '', '',
                            $item->order_id, $item->shipping_name, $item->shipping_address1,
                            $item->shipping_address3 . ' ' . $item->shipping_address2,
                            $item->shipping_state_or_region, $item->shipping_city,
                            $item->shipping_postal_code, '', $item->shipping_country,
                            $item->shipping_phone,
                          ];

                    }


                    if($system == 'birdsystem') {
                        $time = new DateTime($item->created_at);
                        $item->created_at = $time->format( DateTime::ISO8601 );
                        $rows = [
                            $item->order_id, $item->item_id, $item->created_at,
                            $item->created_at, $item->created_at, $item->created_at,
                            '', $item->email, $item->name, $item->phone, $item->sku,
                            $item->product_name, $item->quantity, '0', $item->quantity,
                            $item->shipment_level, $item->shipping_name, $item->shipping_address1,
                            $item->shipping_address2, $item->shipping_address3, $item->shipping_city,
                            $item->shipping_state_or_region, $item->shipping_postal_code,
                            $item->shipping_country, $item->from
                            ];

                    }

                    $j = 0;
                    foreach ($rows as $row) {
                        $j++;
                        $cell = static::_autoCell($j) . $i;
                        $objPHPExcel->getActiveSheet()->SetCellValue($cell, $row);
                    }

                    $order_ids[] = $item->id;

                    //fputcsv($fp, $row, "\t");
                }

                //fclose($fp);
                $e = new  PHPExcel_Writer_Excel5($objPHPExcel);
                $e->save($filepath);

                // 入库
                $total = count( $order_ids );
                $data = [
                    'filename'   => $filename,
                    'total'      => $total,
                    'user_id'    => 1,
                    'order_ids'  => implode(',', $order_ids),
                    'created_at' => date('Y-m-d H:i:s'),
                    ];
                DB::table('logistics_file')->insert( $data );

                // 更新订单状态
                DB::table('orders')->where_in('id', $order_ids)
                                   ->update(['order_status' => 'exported']);

                $result[] = ['name' => $system, 'filename' => $filename, 'total' => $total];
                
            }
        
        }

        return $result;

    }

    private static function _autoCell($n) {
       $n--;
       for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
            $r = chr($n%26 + 0x41) . $r;
       return $r;
    }

}
?>
