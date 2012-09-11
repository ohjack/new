<?php

class User {

    /**
     * 通过用户ID获取用户销售平台
     *
     * @param: $user_id integer 用户ID
     *
     * return $platform object
     */
    public static function getPlatform($user_id) {

        $fields = ['platform.name', 'platform.option', 'users_platform.option as user_option'];

        $platform = DB::table('users_platform')->left_join('platform', 'users_platform.platform_id', '=', 'platform.id')
                                               ->where('users_platform.user_id', '=', $user_id)
                                               ->get($fields);

        return $platform;
    
    }
}

?>
