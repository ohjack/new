<?php
use Laravel\Redirect;

use Laravel\Input;

use Laravel\View;

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
        $username=Input::get('username');
        $password=Input::get('password');
        if(User::login($username, $password))
        {
            return Redirect::to('order');
        }
    }
}
?>
