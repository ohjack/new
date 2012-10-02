<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <title>用户登录</title>
</head>
<body>
<div style="margin-top: 200px">
  <div style="margin:0 auto; width: 300px; border: 1px solid #ccc;">
    <h2 style="text-align: center; margin: 5px; padding-top: 20px">用户登录</h2>
  {{ Form::open('login/submit') }}  
    <table style="padding:0 20px 20px 20px">
      <tr>
        <th style="text-align: right">帐号：</th>
        <td>{{ Form::text('username') }}</td>
      </tr>
      <tr>
        <th style="text-align: right">密码：</th>
        <td>{{ Form::password('password') }}</td>
      </tr>
      <tr>
        <td colspan="2">{{ Form::submit('登录') }} {{ HTML::link('register', '用户注册', ['style'=>'margin-left: 20px']) }}</td>
      </tr>
    </table>
    {{ Form::close() }}
  </div>
</div>
  
</body>
</html>
