<?php

class User {

    public static function getPlatform($user_id) {
        $platform = DB::table('users_platform')->left_join('platform', 'users_platform.platform_id', '=', 'platform.id')
                                                 ->where('users_platform.user_id', '=', $user_id)
                                                 ->get(['platform.name', 'platform.option', 'users_platform.option as user_option']);

        return $platform;
    
    }
}

?>
