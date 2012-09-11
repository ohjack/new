<?php

class SkuMap {

    public static function saveMap( $data ) {

        // trim
        foreach( $data as $key => $value ) {
            $data[$key] = trim($value);
        }

        DB::table('sku_map')->insert($data);
    }

    public static function chkMap( $sku, $logistics ) {
        $count = DB::table('sku_map')->where('original_sku', '=', $sku)
                                     ->where('logistics', '=', $logistics)
                                     ->count();

        return $count;
    }

}
?>
