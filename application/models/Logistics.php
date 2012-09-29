<?php

class Logistics {

    const PENDING_ORDER   = 0;
    const HAD_MATCH_ORDER = 1;

    /**
     * 统计两物流的数量
     *
     * @param: $user_id integer 用户ID
     *
     * return integer
     */
    public static function getTotal( $user_id ) {
    
        $logistics = ['coolsystem', 'birdsystem'];

        return DB::table('orders')->where_in('logistics', $logistics)
                                  ->where('order_status', '=', self::HAD_MATCH_ORDER)
                                  ->where('user_id', '=', $user_id)
                                  ->count();
    }

    /**
     * 生成物流导入文件
     *
     * @param: $user_id array 用户ID
     * @param: $systems array 系统 暂时是酷&鸟系统
     *
     * return array
     */
    public static function getXlsFile($user_id, $logistics ) {

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
        foreach($logistics as $logistic) {
            $items = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id')
                                       ->left_join('sku_map', 'items.sku', '=', 'sku_map.original_sku')
                                       ->where('orders.user_id', '=', $user_id)
                                       ->where('orders.logistics', '=', $logistic)
                                       ->where('orders.order_status', '=', self::HAD_MATCH_ORDER)
                                       ->where('sku_map.logistics', '=', $logistic)
                                       ->get($fields[$logistic]);

            if( $items ) {
                $filename = sprintf('%s_%s_%s.xlsx', $logistic, $user_id, date('Y_m_d'));
                $filepath = path('public') . 'data' . DS . 'logistics_file' . DS . $filename;
                $objPHPExcel->setActiveSheetIndex(0);

                $i = 0;
                foreach ($first_row[$logistic] as $row) {
                    $i++;
                    $cell = static::_autoCell($i) . '1';
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($cell, $row, PHPExcel_Cell_DataType::TYPE_STRING);
                }
                $order_ids = [];
                $i = 1;
                foreach ($items as $item) {
                    $i++;
                    if($logistic == 'coolsystem') {
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


                    if($logistic == 'birdsystem') {
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
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($cell, $row, PHPExcel_Cell_DataType::TYPE_STRING);
                    }

                    $order_ids[] = $item->id;

                }

                $PHPExcel_Writer = new  PHPExcel_Writer_Excel5($objPHPExcel);
                $PHPExcel_Writer->save($filepath);

                // 统计
                $total = count( $order_ids );

                $result[] = ['name' => $logistic, 'filename' => $filename, 'total' => $total];
            }
        }

        return $result;
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
