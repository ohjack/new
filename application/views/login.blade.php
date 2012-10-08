@layout('layout')
@section('script')
{{ HTML::script('js/files/login.js') }}
{{ HTML::script('js/files/functions.js') }}
@endsection
@section('content')
<!-- Login wrapper begins -->
<div class="loginWrapper">
    <!-- Current user form -->
    {{ Form::open('login/submit', 'POST', ['id' => 'login']) }}
        <!--div class="loginPic">
            <a href="#" title=""><img src="{{ URL::base() }}/images/userLogin.png" alt=""></a>
            <span>Eimo</span>
            <div class="loginActions">
                <div><a href="#" title="切换用户" class="logleft flip"></a></div>
                <div><a href="#" title="忘记密码?" class="logright"></a></div>
            </div>
        </div-->
        
        <input type="text" name="username" placeholder="帐号" class="validate[required] loginUsername" id="username">
        <input type="password" name="password" placeholder="密码" class="validate[required] loginPassword" id="password">
        
        <div class="logControl">
            <div class="memory">
                <input name="remember" type="checkbox" checked="checked" class="check" id="remember1" style="opacity: 0; ">
                <label for="remember1">下次自动登录</label>
            </div>
            <input type="submit" name="submit" value="登录" class="buttonM bBlue">
            <div class="clear"></div>
        </div>
    {{ Form::close() }}
    
    <!-- New user form -->
    {{ Form::open('login/submit', 'POST', ['id' => 'recover']) }}
        <div class="loginPic">
            <a href="#" title=""><img src="{{ URL::base() }}/images/userLogin2.png" alt=""></a>
            <div class="loginActions">
                <div><a href="#" title="" class="logback flip"></a></div>
                <div><a href="#" title="Forgot password?" class="logright"></a></div>
            </div>
        </div>
            
        <input type="text" name="username" placeholder="帐号" class="loginUsername">
        <input type="password" name="password" placeholder="密码" class="loginPassword">
        
        <div class="logControl">
            <div class="memory">
                <input type="checkbox" checked="checked" class="check" id="remember2" style="opacity: 0; ">
                <label for="remember2">下次自动登录</label>
            </div>
            <input type="submit" name="submit" value="登录" class="buttonM bBlue">
        </div>
    {{ Form::close() }}

</div>
<!-- Login wrapper ends -->
@endsection
