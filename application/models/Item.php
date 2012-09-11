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
            'items.id',
            'items.sku',
            'items.name',
            'orders.from',
            DB::raw('count(items.id) as count')
        ];

        $had_sku_items = DB::table('items')->left_join('sku_map', 'items.sku', '=', 'sku_map.original_sku')
                                           ->where_not_null('sku_map.target_sku')
                                           ->get(['items.id']);

        $not_in = [0];
        foreach ($had_sku_items as $item) {
            $not_in[] = $item->id;
        }

        // 待整理
        $items = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id')
                                   ->where_not_in('items.id', $not_in)
                                   ->where(DB::raw("((orders.shipping_country"), '=', 'US')
                                   ->where('orders.from', '=', DB::raw("'Amazon.com')"))
                                   ->or_where(DB::raw("(orders.from"), '=', DB::raw("'Amazon.co.uk'))"))
                                   ->group_by('items.sku')
                                   ->group_by('orders.from')
                                   ->get($fields);

        return $items;
    }

}

?>
