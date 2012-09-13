<?php

class Item {

    /**
     * 获取没有SKU映射的产品
     *  
     * return $items object
     *
     */
    public static function getNoSkuItems() {

        $fields = [
            'items.sku',
            'items.name',
            'orders.shipping_country',
            'orders.from',
        ];


        $items = DB::table('items')->left_join('orders', 'items.order_id', '=', 'orders.id')
                                   ->group_by('items.sku')
                                   ->group_by('orders.from')
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
