<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="viewport" content="width=1024, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>用户登录</title>
    <!--Stylesheets-->
    {{ HTML::style('css/reset.css') }}
    {{ HTML::style('css/icons.css') }}
    {{ HTML::style('css/main.css') }}
    {{ HTML::style('css/formalize.css') }}
    <!--JavaScript-->
    {{ HTML::script('js/jquery.js') }}
    {{ HTML::script('js/jqueryui.min.js') }}
    {{ HTML::script('js/jquery.validate.js') }}
    <script src="/js/jquery.cookies.js"></script>
    <script src="/js/jquery.pjax.js"></script>
    <script src="/js/formalize.min.js"></script>
    <script src="/js/jquery.metadata.js"></script>
    <script src="/js/jquery.checkboxes.js"></script>
    <script src="/js/jquery.chosen.js"></script>
    <script src="/js/jquery.fileinput.js"></script>
    <script src="/js/jquery.datatables.js"></script>
    <script src="/js/jquery.sourcerer.js"></script>
    <script src="/js/jquery.tipsy.js"></script>
    <script src="/js/jquery.calendar.js"></script>
    <script src="/js/jquery.inputtags.min.js"></script>
    <script src="/js/jquery.wymeditor.js"></script>
    <script src="/js/jquery.livequery.js"></script>
    <script src="/js/jquery.flot.min.js"></script>
    <script src="/js/application.js"></script>
</head>
<body id="login">
  <div id="login_container">
    <div id="login_form">
    {{ Form::open('login/submit') }}
      <p>
        <input type="text" id="username" name="username" placeholder="帐号" class="{validate: {required: true}}">
      </p>
      <p>
        <input type="password" id="password" name="password" placeholder="密码" class="{validate: {required: true}}" />
      </p>
      <button type="submit" class="button blue"><span class="glyph key"></span> 登录</button>
    {{ Form::close() }}
    </div>
  </div>
</div>
</body>
</html>
