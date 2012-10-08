<?php

class Login_Controller extends Base_Controller
{
    public function action_index()
    {
        if (Sentry::check())
        {
            return Redirect::to('order');
        }
        return View::make('login');
    }
    
    public function action_submit()
    {
        $username = Input::get('username');
        $password = Input::get('password');

        $result = User::login($username, $password);
        if($result['success'])
        {
            $message = '登录成功，跳转中...';
            $button = ['name' => '确定', 'link' => URL::base() ];
            return View::make('message')->with('message', $message)->with('button', $button);
        } else {
            return View::make('message')->with('message', $result['message']);
        }
    }
}
?>
