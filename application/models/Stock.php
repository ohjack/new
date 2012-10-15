<?php

class Stock {

    /**
     * 获取平台库存信息
     *
     * @param: $user_id     integer 用户ID
     * @param: $platform_id integer 平台ID
     *
     * return object
     */
    public static function getStock($user_id, $platform_id) {
        return DB::table('stock')->where('user_id', '=', $user_id)
                                 ->where('platform_id', '=', $platform_id)
                                 ->get();
    }

   /**
    * 从仓储获取库存
    *
    * @param: $user_platform array 用户库存平台数据
    *
    * return void
    */
    public static function importStock( $user_platforms ) {

        foreach ($user_platforms as $user_platform) {
            $spider_name = 'Spider_Report_' . $user_platform->type;
            $stock_spider = new Spider_Report( new $spider_name);
            echo '--- START PLATFORM --- ' . $user_platform->name . "\n";

            $base_options = array_merge(unserialize($user_platform->option), unserialize($user_platform->user_option));
            try {
                $stock_datas = $stock_spider->getStockReport( $base_options );
                if($stock_datas) {
                    // 清除原来的库存信息
                    DB::table('stock')->where('platform_id', '=', $user_platform->id)->delete();

                    // 添加用户信息
                    $insert_datas = [];
                    foreach($stock_datas as $data) {
                        $data['user_id'] = $user_platform->user_id;
                        $data['platform_id'] = $user_platform->id;
                        $insert_datas[] = $data;
                    }
                    echo 'INSERT ' . $user_platform->name . "\n";
                    DB::table('stock')->insert($insert_datas);
                }
            } catch (Exception $e) {
                print_r($e);
            }
            
        }
    
    }

}
?>
