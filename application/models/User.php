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

        $fields = ['p.type', 'p.name', 'p.option', 'up.id', 'up.user_id', 'up.option as user_option','up.platform_id'];

        $platforms = DB::table('platform as p')->left_join('users_platform as up', 'p.id', '=', 'up.platform_id')
                                               ->where('up.user_id', '=', $user_id)
                                               ->get( $fields );

        return $platforms;
    }
    
    /**
     * 通过用户平台ID获取具体用户平台信息
     * 
     * @param  $userplatfrom_id
     */
    public static function getPlatform_one($userplatform_id)
    {
          $fields = ['p.type', 'p.name', 'p.option', 'up.id', 'up.user_id', 'up.option as user_option','up.platform_id'];

       return $platforms = DB::table('platform as p')->left_join('users_platform as up', 'p.id', '=', 'up.platform_id')
                                                     ->where('up.id','=',$userplatform_id)                                          
                                                     ->first( $fields );
    }
    /**
     * 配置用户平台信息
     * @param 会员ID $user_id
     * @param 配置数据 $option
     * @param 平台ID  $platform_id
     * 
     * return void
     */
    public static function setPlatform($user_id,$platform_id,$option,$id=0)
    {
        if($id!=0)
        {
            //更新配置信息
            self::modifyPlatform($id, $option);
        }
        else
        {
            //插入配置信息
            $option['user_id']=$user_id;
            $option['platform_id']=$platform_id;
            self::insertPlatform($option);
        }
    }
    
    /**
     * 添加平台配置
     * @param 配置数据 $data
     */
    private static function insertPlatform($data)
    {
        DB::table('users_platform')->insert($data);
    }
    
    /**
     * 修改平台配置
     * @param  $id 用户平台ID
     * 
     * @param  $option        平台数据
     */
    private static function modifyPlatform($id,$option)
    {   

        DB::table('users_platform')->where('id','=',$id)
                                   ->update($option);
    }
    
    /**
     * 是否存在平台信息
     * @param  $id        用户平台ID
     * 
     */
    private static function existPlatform($id)
    {
        return DB::table('users_platform')->where('id','=',$user_id)
                                          ->first();
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
