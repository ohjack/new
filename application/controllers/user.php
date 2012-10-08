<?php

use Laravel\Input;

use Laravel\Redirect;

use Laravel\View;

class User_Controller extends Base_Controller {

    public function action_index() {
        echo 'user controller';
        return ;
    }
    public function action_platform()
    {
        $user_id=Sentry::user()->get('id');
        $platforms=User::getPlatforms($user_id);
        $sysPlatforms=Platform::getAll();
        return View::make('platform')->with('platforms',$platforms)
                                     ->with('sysPlatforms',$sysPlatforms)
                                     ->with('title','平台设置');
    }
    
    public function action_platformadd()
    {
        $platform_id=Input::get('platform_id');
        $platform=Platform::getByID($platform_id);
        $platform_name=$platform->name;
        $option=unserialize($platform->option);
        return view::make('platform.add')->with('platform_id',$platform_id)
                                         ->with('userplatform_id','')
                                         ->with('platform_name',$platform_name)   
                                         ->with('option',$option)
                                         ->with('title','添加平台信息');
    }
    public function action_platformedit()
    {
   
        $userplatform_id=Input::get('userplatform_id');
        $platform=User::getPlatform_one($userplatform_id);
        $platform_name=$platform->name;
        $option=unserialize($platform->user_option);
        return view::make('platform.add')->with('platform_id','')
                                         ->with('userplatform_id',$userplatform_id)   
                                         ->with('platform_name',$platform_name)
                                         ->with('option',$option)
                                         ->with('title','修改平台信息');
    }
    
    public function action_platformmod()
    {
        if($_POST)
        {
            $user_id=Sentry::user()->get('id');
            $data=[
            'AWSAccessKeyId'=>Input::get('AWSAccessKeyId'),
            'MarketplaceId.Id.1'=>Input::get('MarketplaceId_Id_1'),
            'SellerId'=>Input::get('SellerId'),
            'Key'=>Input::get('Key'),
            ];
            $data=['option'=>serialize($data)];
            $platform_id=Input::get('platform_id');
            $userplatform_id=Input::get('userplatform_id');
            if(!empty($userplatform_id)){
            User::setPlatform($user_id, $platform_id, $data,$userplatform_id);
            }
            else {
            User::setPlatform($user_id, $platform_id, $data);
            }    
            return Redirect::to('user/platform');
        }
    }
}

?>
