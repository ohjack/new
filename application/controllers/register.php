<?php
use Laravel\Redirect;

use Laravel\Response;

use Laravel\Input;

class Register_Controller extends Base_Controller
{
      public function action_index()
      {
          return  View::make('register');
      }

      public function action_submit()
      {
           $username=Input::get('username');
           $email=Input::get('email');
           $password=Input::get('password');
           
           $info=User::register($username, $email, $password);

           
           if($info['result'])
           {
               echo "<script>alert('注册成功')</script>";
               return Redirect::to('/login');
           }
      }
}
?>