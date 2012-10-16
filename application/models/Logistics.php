<?php

class Logistics {

    /**
     * 统计新增的物流信息数量
     *
     * @param: $user_id integer 用户ID
     *
     * return integer
     */
    public static function total( $user_id ) {
    
        $logistics = Config::get('application.logistics');

        $total = 0;
        foreach ($logistics as $code => $logistic) {
            $total += static::count( $user_id, $code );
        }

        return $total;
    }

    /**
     * 导出物流信息列表
     *
     * @param: $user_id integer 用户ID
     * 
     * return array
     */
    public static function exportList( $user_id ) {
        $logistics = Config::get('application.logistics');

        $lists = [];
        foreach ($logistics as $code => $logistic) {
            $total = static::count($user_id, $code);
            if($total) {
                $lists[$code] = [
                    'name'  => $logistic,
                    'total' => $total
                    ];
            }
        }

        return $lists;
    }

    /**
     * 统计指定物流新增记录
     *
     * @param: $user_id   integer 用户ID
     * @param: $logistics string  物流名称
     * 
     * return integer
     */
    public static function count($user_id, $logistics) { 

        $last_item_id = static::_lastExport( $user_id, $logistics);

        $total = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id')
                                    ->where('items.id', '>', $last_item_id)
                                    ->where('orders.logistics', '=', $logistics)
                                    ->where('orders.order_status', '=', HAD_MATCH_ORDER)
                                    ->where('orders.user_id', '=', $user_id)
                                    ->count();

        return $total;
    }

    /**
     * 生成并下载物流导出文件
     *
     * @param: $user_id array 用户ID
     * @param: $systems array 系统 
     *
     * return void
     */
    public static function download($user_id, $logistics ) {

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
            'micaosystem' => [
                '投保易网邮保险服务', '邮件编号', '收件人姓名', '收件人地址', '收件人电话', '收件人国家',
                '邮编', '件数', '重量', '海关品名', '物品数量', '申报单价', '币种', '产品标识【配货信息】'
                ]
            ];

        $fields = [
            'coolsystem' => [
                'items.id', 'orders.entry_id as order_id', 'sku_map.target_sku as sku', 'items.quantity', 
                'orders.shipping_name', 'items.entry_id as item_id', 'orders.shipping_address1', 
                'orders.shipping_address2', 'orders.shipping_address3', 'orders.shipping_state_or_region', 
                'orders.shipping_city', 'orders.shipping_postal_code', 'orders.shipping_country', 
                'orders.shipping_phone', 'orders.shipment_level'
                ],
            'birdsystem' => [
                'items.id', 'orders.entry_id as order_id', 'items.entry_id as item_id',
                'orders.created_at', 'orders.shipment_level', 'orders.email',
                'orders.name', 'orders.shipping_phone as phone', 'sku_map.target_sku as sku',
                'items.name as product_name', 'items.quantity', 'orders.shipment_level',
                'orders.shipping_name', 'orders.shipping_address1', 'orders.shipping_address2',
                'orders.shipping_address3', 'orders.shipping_city', 'orders.shipping_state_or_region',
                'orders.shipping_postal_code', 'orders.shipping_country', 'orders.from', 'orders.shipment_level'
                ],
            'micaosystem' => [
                'items.id', 'orders.entry_id as order_id', 'orders.shipping_name', 'orders.shipping_address3', 
                'orders.shipping_address2', 'orders.shipping_address1', 'orders.shipping_city', 
                'orders.shipping_state_or_region', 'orders.shipping_phone', 'orders.shipping_country', 
                'orders.shipping_postal_code', 'sku_map.product_name', 'items.quantity', 'sku_map.product_price', 
                'items.currency', 'orders.shipment_level'
                ]
            ];


        $item_id = static::_lastExport( $user_id, $logistics );

        $items = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id')
                                   ->left_join('sku_map', 'items.sku', '=', 'sku_map.original_sku')
                                   ->where('orders.user_id', '=', $user_id)
                                   ->where('orders.logistics', '=', $logistics)
                                   ->where('orders.order_status', '=', HAD_MATCH_ORDER)
                                   ->where('sku_map.logistics', '=', $logistics)
                                   ->where('items.id', '>', $item_id)
                                   ->get($fields[$logistics]);

        if( $items ) {
            $filename = sprintf('%s_%s_%s.xls', $logistics, $user_id, date('Y_m_d_H_i_s_') . rand(1, 1000));
            $filepath = path('public') . 'data' . DS . 'logistics_file' . DS . $filename;
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);

            // 写第一行
            $i = 0;
            foreach ($first_row[$logistics] as $row) {
                $i++;
                $cell = static::_autoCell($i) . '1';
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($cell, $row, PHPExcel_Cell_DataType::TYPE_STRING);
            }

            // 写表格内容
            $i = 1;
            $last_item_id = 0;
            foreach ($items as $item) {

                $i++;
                if($logistics == 'coolsystem') {
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


                if($logistics == 'birdsystem') {
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

                if($logistics == 'micaosystem') {
                    $rows = [
                        'N', $item->order_id, $item->shipping_name, $item->shipping_address3 . ' ' . $item->shipping_address2 .
                        ' ' . $item->shipping_address1 . ' ' . $item->shipping_city . ' ' . 
                        $item->shipping_state_or_region, $item->shipping_phone, $item->shipping_country,
                        $item->shipping_postal_code, '1', '0.5', $item->product_name, $item->quantity, 
                        $item->product_price, $item->currency, ''
                        ];
                }

                $j = 0;
                foreach ($rows as $row) {
                    $j++;
                    $cell = static::_autoCell($j) . $i;
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($cell, $row, PHPExcel_Cell_DataType::TYPE_STRING);

                    // 加急订单标记颜色
                    if($item->shipment_level == 'Expedited') {
                        $column = static::_autoCell($j).$i;
                        $objPHPExcel->getActiveSheet()->getStyle($column)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
                    }

                }



                $last_item_id = max($last_item_id, $item->id); // 最大的item id记录
            }

            $PHPExcel_Writer = new  PHPExcel_Writer_Excel5($objPHPExcel);
            $PHPExcel_Writer->save($filepath);

            // 物流信息导出记录
            $data = [
                'total'       => count($items), 
                'filename'    => $filename, 
                'user_id'     => $user_id, 
                'logistics'   => $logistics,
                'item_id'     => $last_item_id,
                'export_date' => date('Y-m-d H:i:s')
                ];

            DB::table('orders_export')->insert($data);

            header('Content-type: application/vnd.ms-excel;charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            readfile($filepath);  
        }
    }

    public static function downloadFile( $filename ) {
        $filepath = path('public') . 'data' . DS . 'logistics_file' . DS . $filename;

        if(file_exists($filepath)) {
            header('Content-type: application/vnd.ms-excel;charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            readfile($filepath);
        } else {
            exit('没有此文件');
        }
    }

    /**
     * 获取历史订单
     *
     * @param: $user_id integer 用户ID
     *
     * return array
     */
    public static function histories( $user_id ) {
        $logistics = Config::get('application.logistics');

        $size = 5; // 取最近5条历史
        $lists = [];
        foreach ($logistics as $code => $logistic) {
             $history = DB::table('orders_export')->where('user_id', '=', $user_id)
                                                  ->where('logistics', '=', $code)
                                                  ->order_by('id', 'DESC')
                                                  ->take($size)
                                                  ->get();
             if($history) $lists[$code] = $history;
        }

        return $lists;
    }

    /**
     * 最后一次导出的order_id
     *
     * @param: $user_id integer 用户ID
     *
     * return integer
     */
    private static function _lastExport( $user_id, $logistics ) {

        $item_id = DB::table('orders_export')->where('user_id', '=', $user_id)
                                              ->where('logistics', '=', $logistics)
                                              ->order_by('id', 'DESC')
                                              ->take(1)
                                              ->only('item_id'); 

        return $item_id ? $item_id : 0;
    }

    /**
     * 获取表格列字母
     *
     * 如第1列:返回A  第26列:返回Z 第27列:返回AA
     *
     * @param: $n integer 第几列
     *
     * return string
     */
    private static function _autoCell($n) {
       $n--;
       for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
            $r = chr($n%26 + 0x41) . $r;
       return $r;
    }

}
?>
