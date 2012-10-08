<?php

class Setting {
    
    /**
     * 获取用户配置
     *
     * @param: $user_id integer 用户ID
     * @param: $name    string  配置类型
     * 
     * reutrn array
     */
    public static function orderList( $user_id, $name ) {
        $values = DB::table('users_setting')->where('user_id', '=', $user_id)
                                            ->where('name', '=', $name)
                                            ->only('values');

        return unserialize($values);
    }
}
?>
