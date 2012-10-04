<?php

class SkuMap {

    /**
     * 获取所有映射列表
     *
     */
    public static function getMaps( $per_page, $options ) {

        $obj = DB::table('sku_map');
        foreach ($options as $key => $option) {
            if(trim($option))
                $obj = $obj->where($key, '=', $option);
        }
        $obj = $obj->where('user_id','=',Sentry::user()->get('id'));
        return $obj->paginate( $per_page );

    }

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

    /**
     * 更新sku  map
     *
     * @param: $options array 更新条件
     * @param: $data    array 更新数据
     *
     * return string 
     */
    public static function updateMap( $options, $data ) {

        //$exsits = static::chkMap( $data['original_sku'], $data['logistics']);
        if(empty($options)) {
            $return = 'error';
        //else if($exsits) {
        //    $return =  'exsits';
        } else {
            $table = DB::table('sku_map');
            foreach ($options as $key=>$option) {
                $table = $table->where($key, '=', $option);
            }
            $table->update($data);
            
            $return = 'ok';
        }

        return $return;
    }

    /**
     * 根据条件删除sku map
     *
     * @param: $options  array  条件设置
     *
     * return string 
     */
    public static function deleteMap( $options ) {
        if( empty($options) ) {
            $return = 'error';
        } else {
            $table = DB::table('sku_map');

            foreach ($options as $key => $option) {
                $table = $table->where($key, '=', $option);
            }

            $table->delete();

            $return = 'ok';
        }

        return $return;
    }
}
?>
