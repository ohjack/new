<?php

class Setting {

    /**
     * 初始化系统设置
     */
    public static function init() {
        // 订单列表字段
        Config::set('order_list_fields', static::get('order_list_fields'));
    }

    /**
     * 获取系统配置
     *
     */
    public static function get( $name ) {
        return unserialize(DB::table('setting')->where('name', '=', $name)->only('values'));
    }
    
    /**
     * 获取用户配置
     *
     * @param: $user_id integer 用户ID
     * @param: $name    string  配置类型
     * 
     * reutrn array
     */
    public static function getUserSetting( $user_id, $name ) {
        $values = DB::table('users_setting')->where('user_id', '=', $user_id)
                                            ->where('name', '=', $name)
                                            ->only('values');

        return @unserialize($values);
    }

    /**
     * 添加用户配置
     *
     * @param: $user_id integer 用户ID
     * @param: $name    string  设置类型
     * @param: $values  array   设置值
     *
     * return void
     */
    public static function setUserSetting( $user_id, $name, $values ) {
        $values = serialize($values);

        if(static::getUserSetting( $user_id, $name)) {
            $data = ['values' => $values];
            DB::table('users_setting')->where('user_id', '=', $user_id)
                                      ->where('name', '=', $name)
                                      ->update($data);
        } else {
            $data = ['user_id' => $user_id, 'name'=>$name, 'values' => $values];
            DB::table('users_setting')->insert($data);
        }
    }
}
?>
