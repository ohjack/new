<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>EIMO电商系统</title>
    <!--[if IE]> <link href="css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->
    {{ HTML::style('css/styles.css') }}
    {{ HTML::script('js/files/jquery.min.js') }}
    {{ HTML::script('js/plugins/forms/ui.spinner.js') }}
    {{ HTML::script('js/plugins/forms/jquery.mousewheel.js') }}
    <script type="text/javascript" src="/js/files/jquery-ui.min.js"></script>

    <script type="text/javascript" src="/js/plugins/charts/excanvas.min.js"></script>
    <script type="text/javascript" src="/js/plugins/charts/jquery.flot.js"></script>
    <script type="text/javascript" src="/js/plugins/charts/jquery.flot.orderBars.js"></script>
    <script type="text/javascript" src="/js/plugins/charts/jquery.flot.pie.js"></script>
    <script type="text/javascript" src="/js/plugins/charts/jquery.flot.resize.js"></script>
    <script type="text/javascript" src="/js/plugins/charts/jquery.sparkline.min.js"></script>


    <script type="text/javascript" src="/js/plugins/tables/jquery.dataTables.js"></script>
    <script type="text/javascript" src="/js/plugins/tables/jquery.dataTables.columnFilter.js"></script>
    <script type="text/javascript" src="/js/plugins/tables/jquery.sortable.js"></script>
    <script type="text/javascript" src="/js/plugins/tables/jquery.resizable.js"></script>

    <script type="text/javascript" src="/js/plugins/forms/autogrowtextarea.js"></script>
    <script type="text/javascript" src="/js/plugins/forms/jquery.uniform.js"></script>
    <script type="text/javascript" src="/js/plugins/forms/jquery.inputlimiter.min.js"></script>
    <script type="text/javascript" src="/js/plugins/forms/jquery.tagsinput.min.js"></script>
    <script type="text/javascript" src="/js/plugins/forms/jquery.maskedinput.min.js"></script>
    <script type="text/javascript" src="/js/plugins/forms/jquery.autotab.js"></script>
    <script type="text/javascript" src="/js/plugins/forms/jquery.chosen.min.js"></script>
    <script type="text/javascript" src="/js/plugins/forms/jquery.dualListBox.js"></script>
    <script type="text/javascript" src="/js/plugins/forms/jquery.cleditor.js"></script>
    <script type="text/javascript" src="/js/plugins/forms/jquery.ibutton.js"></script>
    <script type="text/javascript" src="/js/plugins/forms/jquery.validationEngine-cn.js"></script>
    <script type="text/javascript" src="/js/plugins/forms/jquery.validationEngine.js"></script>

    <script type="text/javascript" src="/js/plugins/uploader/plupload.js"></script>
    <script type="text/javascript" src="/js/plugins/uploader/plupload.html4.js"></script>
    <script type="text/javascript" src="/js/plugins/uploader/plupload.html5.js"></script>
    <script type="text/javascript" src="/js/plugins/uploader/jquery.plupload.queue.js"></script>


    <script type="text/javascript" src="/js/plugins/wizards/jquery.form.wizard.js"></script>
    <script type="text/javascript" src="/js/plugins/wizards/jquery.validate.js"></script>
    <script type="text/javascript" src="/js/plugins/wizards/jquery.form.js"></script>


    {{ HTML::script('js/plugins/ui/jquery.collapsible.min.js') }}
    {{ HTML::script('js/plugins/ui/jquery.breadcrumbs.js') }}
    {{ HTML::script('js/plugins/ui/jquery.tipsy.js') }}
    {{ HTML::script('js/plugins/ui/jquery.progress.js') }}
    {{ HTML::script('js/plugins/ui/jquery.timeentry.min.js') }}
    {{ HTML::script('js/plugins/ui/jquery.colorpicker.js') }}
    {{ HTML::script('js/plugins/ui/jquery.jgrowl.js') }}
    {{ HTML::script('js/plugins/ui/jquery.fancybox.js') }}
    {{ HTML::script('js/plugins/ui/jquery.fileTree.js') }}
    {{ HTML::script('js/plugins/ui/jquery.sourcerer.js') }}

    <script type="text/javascript" src="/js/plugins/others/jquery.fullcalendar.js"></script>
    <script type="text/javascript" src="/js/plugins/others/jquery.elfinder.js"></script>

    <script type="text/javascript" src="/js/plugins/ui/jquery.easytabs.min.js"></script>
        {{ HTML::script('js/files/bootstrap.js') }}

        {{ HTML::script('js/common.js') }}
        @yield('script')
</head>
    <body>
        <!-- Top line begins -->
        <div id="top">
            <div class="wrapper">
                <a href="{{ URL::base() }}" title="EIMO SYSTEM" class="logo"><img src="{{ URL::base() }}/images/logo.png" alt=""></a>

                <!-- Right top nav -->
                <div class="topNav">
                  <ul class="userNav">
                    @if( Sentry::check() )
                    <li><a href="{{ URL::to('logout') }}" title="登出" class="logout"></a></li>
                    @endif
                  </ul>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <!-- Top line ends -->
        @yield('sidebar')
        @yield('content')
    </body>
</html>
