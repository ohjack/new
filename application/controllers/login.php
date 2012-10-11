<?php

class Login_Controller extends Base_Controller
{
    public function action_index()
    {
        if (Sentry::check())
        {
            return Redirect::to('/');
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
            return Redirect::to('/');
        } else {
            return View::make('message')->with('message', $result['message']);
        }
    }
}
?>
