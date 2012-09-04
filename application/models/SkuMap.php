<?php

class SkuMap {

    public static function saveMap($data) {

        // trim
        foreach($data as $key => $value) {
            $data[$key] = trim($value);
        }

        DB::table('sku_map')->insert($data);
    }

    public static function chkMap($data) {
        $count = DB::table('sku_map')->where('original_sku', '=', $data['original_sku'])
                                     ->where('logistics', '=', $data['logistics'])
                                     ->count();

        return $count;
    }
}
?>
