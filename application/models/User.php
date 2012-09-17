<?php

class User {

    /**
     * 通过用户ID获取用户可抓取销售平台配置
     *
     * @param: $user_id integer 用户ID
     *
     * return array
     */
    public static function getPlatforms($user_id) {

        $fields = [
            'p.type',
            'p.name', 
            'p.option', 
            'up.id',
            'up.option as user_option'
            ];

        $platforms = DB::table('platform as p')->left_join('users_platform as up', 'p.id', '=', 'up.platform_id')
                                            ->where('up.user_id', '=', $user_id)
                                            ->get( $fields );

        return $platforms;
    
    }
}

?>
