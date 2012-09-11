<?php

class SkuMap {

    /**
     * 保存SKU映射关系
     *
     * @param: $data array 映射关系数组
     *
     * return void
     */
    public static function saveMap( $data ) {

        // trim
        foreach( $data as $key => $value ) {
            $data[$key] = trim($value);
        }

        DB::table('sku_map')->insert($data);
    }

    /**
     * 检查某物流映射关系是否存在
     *
     * @param: $sku string 产品sku
     * @param: $logistics string 物流系统
     *
     * return integer
     */
    public static function chkMap( $sku, $logistics ) {
        $count = DB::table('sku_map')->where('original_sku', '=', $sku)
                                     ->where('logistics', '=', $logistics)
                                     ->count();

        return $count;
    }

}
?>
