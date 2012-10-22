<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | The URL used to access your application without a trailing slash. The URL
    | does not have to be set. If it isn't, we'll try our best to guess the URL
    | of your application.
    |
    */

    'url' => '',

    /*
    |--------------------------------------------------------------------------
    | Asset URL
    |--------------------------------------------------------------------------
    |
    | The base URL used for your application's asset files. This is useful if
    | you are serving your assets through a different server or a CDN. If it
    | is not set, we'll default to the application URL above.
    |
    */

    'asset_url' => '',

    /*
    |--------------------------------------------------------------------------
    | Application Index
    |--------------------------------------------------------------------------
    |
    | If you are including the "index.php" in your URLs, you can ignore this.
    | However, if you are using mod_rewrite to get cleaner URLs, just set
    | this option to an empty string and we'll take care of the rest.
    |
    */

    'index' => 'index.php',

    /*
    |--------------------------------------------------------------------------
    | Application Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the encryption and cookie classes to generate secure
    | encrypted strings and hashes. It is extremely important that this key
    | remains secret and it should not be shared with anyone. Make it about 32
    | characters of random gibberish.
    |
    */

    'key' => 'x23iUuf239er8se4i@21dg6rewvjju7e',

    /*
    |--------------------------------------------------------------------------
    | Profiler Toolbar
    |--------------------------------------------------------------------------
    |
    | Laravel includes a beautiful profiler toolbar that gives you a heads
    | up display of the queries and logs performed by your application.
    | This is wonderful for development, but, of course, you should
    | disable the toolbar for production applications.
    |
    */

    'profiler' => false,

    /*
    |--------------------------------------------------------------------------
    | Application Character Encoding
    |--------------------------------------------------------------------------
    |
    | The default character encoding used by your application. This encoding
    | will be used by the Str, Text, Form, and any other classes that need
    | to know what type of encoding to use for your awesome application.
    |
    */

    'encoding' => 'UTF-8',

    /*
    |--------------------------------------------------------------------------
    | Default Application Language
    |--------------------------------------------------------------------------
    |
    | The default language of your application. This language will be used by
    | Lang library as the default language when doing string localization.
    |
    */

    'language' => 'cn',

    /*
    |--------------------------------------------------------------------------
    | SSL Link Generation
    |--------------------------------------------------------------------------
    |
    | Many sites use SSL to protect their users' data. However, you may not be
    | able to use SSL on your development machine, meaning all HTTPS will be
    | broken during development.
    |
    | For this reason, you may wish to disable the generation of HTTPS links
    | throughout your application. This option does just that. All attempts
    | to generate HTTPS links will generate regular HTTP links instead.
    |
    */

    'ssl' => true,

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | The default timezone of your application. The timezone will be used when
    | Laravel needs a date, such as when writing to a log file or travelling
    | to a distant star at warp speed.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | Here, you can specify any class aliases that you would like registered
    | when Laravel loads. Aliases are lazy-loaded, so feel free to add!
    |
    | Aliases make it more convenient to use namespaced classes. Instead of
    | referring to the class using its full namespace, you may simply use
    | the alias defined here.
    |
    */

    'aliases' => array(
        'Auth'          => 'Laravel\\Auth',
        'Authenticator' => 'Laravel\\Auth\\Drivers\\Driver',
        'Asset'         => 'Laravel\\Asset',
        'Autoloader'    => 'Laravel\\Autoloader',
        'Blade'         => 'Laravel\\Blade',
        'Bundle'        => 'Laravel\\Bundle',
        'Cache'         => 'Laravel\\Cache',
        'Config'        => 'Laravel\\Config',
        'Controller'    => 'Laravel\\Routing\\Controller',
        'Cookie'        => 'Laravel\\Cookie',
        'Crypter'       => 'Laravel\\Crypter',
        'DB'            => 'Laravel\\Database',
        'Eloquent'      => 'Laravel\\Database\\Eloquent\\Model',
        'Event'         => 'Laravel\\Event',
        'File'          => 'Laravel\\File',
        'Filter'        => 'Laravel\\Routing\\Filter',
        'Form'          => 'Laravel\\Form',
        'Hash'          => 'Laravel\\Hash',
        'HTML'          => 'Laravel\\HTML',
        'Input'         => 'Laravel\\Input',
        'IoC'           => 'Laravel\\IoC',
        'Lang'          => 'Laravel\\Lang',
        'Log'           => 'Laravel\\Log',
        'Memcached'     => 'Laravel\\Memcached',
        'Paginator'     => 'Laravel\\Paginator',
        'Profiler'      => 'Laravel\\Profiling\\Profiler',
        'URL'           => 'Laravel\\URL',
        'Redirect'      => 'Laravel\\Redirect',
        'Redis'         => 'Laravel\\Redis',
        'Request'       => 'Laravel\\Request',
        'Response'      => 'Laravel\\Response',
        'Route'         => 'Laravel\\Routing\\Route',
        'Router'        => 'Laravel\\Routing\\Router',
        'Schema'        => 'Laravel\\Database\\Schema',
        'Section'       => 'Laravel\\Section',
        'Session'       => 'Laravel\\Session',
        'Str'           => 'Laravel\\Str',
        'Task'          => 'Laravel\\CLI\\Tasks\\Task',
        'URI'           => 'Laravel\\URI',
        'Validator'     => 'Laravel\\Validator',
        'View'          => 'Laravel\\View',
    ),

    /**
     *
     * 物流系统
     *
     */
    'logistics' => [
        'coolsystem' => '酷系统',
        'birdsystem' => '鸟系统',
        'micaosystem' => '米巢系统',
    ],


    /**
     * 
     * 订单处理流程
     *
     *
     */
    'steps' => [
    ],


    /**
     * 系统订单状态
     *
     */
    'order_status' => [
        '0'  => ['desc' => '等待处理',   'define' => 'PENDING_ORDER'],
        '1'  => ['desc' => '已分配物流', 'define' => 'HAD_MATCH_ORDER'],
        '2'  => ['desc' => '部分发货',   'define' => 'PART_SEND_ORDER'],
        '3'  => ['desc' => '已发货',     'define' => 'ALL_SEND_ORDER'],
        '4'  => ['desc' => '先确定发货', 'define' => 'MARK_SEND_ORDER'],
        '5'  => ['desc' => '部分发货',   'define' => ''],
        '6'  => ['desc' => '已发货',     'define' => ''],
        '7'  => ['desc' => '先确定发货', 'define' => ''],
        '8'  => ['desc' => '已取消',     'define' => 'CHANNEL_ORDER']
    ],
        
    /**
     * 物流跟踪状态
     * 
     */
    'track_status'=>[
        '-1'=>'无结果',
        '0'=>'待查询',
        '1'=>'在途中',
        '2'=>'已发货',
        '3'=>'疑难件',
        '4'=>'已签收',
        '5'=>'已退货',
        
    ],

    /*
     * 物流公司代码及运送方式
     */ 
    'logistic_company'=>[
        'UPS'=>[
            'method'=>[
                    '1'=>'First Class',
                    '2'=>'快递',
                    ]
        ],
        'DHL'=>[
            'method'=>[
                    '1'=>'平邮',
                    '2'=>'快递'
            ]
        ],
        'Fedex' => [
            'method' => '',
        ],
        'EMS' => [],
        'USPS' => [],
        'RoyalMail' => [],
        'SingPost' => [],
        'HK Post' => [],
        'EUB' => [],
        'Amazon FBA' => []
    ],
);
