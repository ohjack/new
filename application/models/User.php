<?php

class User {

    /**
     * 通过用户ID获取用户可抓取销售平台配置
     *
     * @param: $user_id integer 用户ID
     *
     * return array
     */
    public static function getPlatforms( $user_id ) {

        $fields = ['p.type', 'p.name', 'p.option', 'up.id', 'up.user_id', 'up.option as user_option'];

        $platforms = DB::table('platform as p')->left_join('users_platform as up', 'p.id', '=', 'up.platform_id')
                                               ->where('up.user_id', '=', $user_id)
                                               ->get( $fields );

        return $platforms;
    }
    
    /*
     * 用户注册
     */
    public static function register($username,$email,$password)
    {
        $return=array('result'=>false,'msg'=>'false');
        if (Sentry::user_exists($username))
        {
            // the user exists
            $return['result']=false;
            $return['msg']='该用户名已被注册';
        }
        
        try
        {
            // create the user
            $user = Sentry::user()->create(array(
                    'email'    => $email,
                    'username'=>$username,
                    'password' => $password,                    
            ));
            if ($user)
            {
                $return['result']=true;
                $return['msg']='注册成功';
            }
            else
            {
                $return['result']=false;
                $return['msg']='注册失败';
            }
        }
        catch (Sentry\SentryException $e)
        {
            $errors = $e->getMessage(); // catch errors such as user exists or bad fields
        }
        
        return $return;
    }

    
    
    /*
     * 用户登录
     */
    
    public static  function login($username,$password)
    {
        
        try
        {
            // log the user in
            $valid_login = Sentry::login($username,$password, true);
            if ($valid_login)
            {
                return true;
                // the user is now logged in - do your own logic
            }
            else
            {
                return false;
                // could not log the user in - do your bad login logic
            }
        }
        catch (Sentry\SentryException $e)
        {
            // issue logging in via Sentry - lets catch the sentry error thrown
            // store/set and display caught exceptions such as a suspended user with limit attempts feature.
            $errors = $e->getMessage();
            echo $errors;
        }
    }
    public static function logout()
    {
        Sentry::logout();
    }
}

?>
