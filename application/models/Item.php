<?php

class Item {

    /**
     * 获取没有SKU映射的产品
     *  
     * 暂时没有分页，因为sql中有group by 导致框架的分页方法无法统计正确的总记录数
     * 等官方修复此bug可修复成正常
     *
     * @param: $per_page integer 每页记录数
     *
     * return $items object
     *
     */
    public static function getNoSkuItems($per_page) {

        $fields = [
            'items.sku',
            'items.name',
            'orders.shipping_country',
            'orders.from',
        ];


        $items = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id')
                                   //->where_not_in('items.sku', DB::table('sku_map')->lists('original_sku'))
                                   ->group_by('items.sku')
                                   ->group_by('orders.from')
                                   //->paginate($per_page, $fields);
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

}

?>
