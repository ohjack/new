<?php

class Item {

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

        $items = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id')
                                   ->where_not_in('items.id', $not_in)
                                   ->where(DB::raw("((orders.shipping_country = 'US' AND orders.from = 'Amazon.com')"), DB::raw("OR"), DB::raw("(orders.from = 'Amazon.co.uk'))"))
                                   ->group_by('items.sku')
                                   ->group_by('orders.from')
                                   ->get($fields);

        return $items;
    }

}

?>
