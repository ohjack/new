<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Simply tell Laravel the HTTP verbs and URIs it should respond to. It is a
| breeze to setup your application using Laravel's RESTful routing and it
| is perfectly suited for building large applications and simple APIs.
|
| Let's respond to a simple GET request to http://example.com/hello:
|
|       Route::get('hello', function()
|       {
|           return 'Hello World!';
|       });
|
| You can even respond to more than one URI:
|
|       Route::post(array('hello', 'world'), function()
|       {
|           return 'Hello World!';
|       });
|
| It's easy to allow URI wildcards using (:num) or (:any):
|
|       Route::put('hello/(:any)', function($name)
|       {
|           return "Welcome, $name.";
|       });
|
*/

Route::get('/', function() {
    return View::make('home.index');
});

Route::get('test', function() {

    $orderSpider = new SpiderOrders(new SpiderOrders_Amazon());
    
    $option = [
        'AWSAccessKeyId' => 'AKIAJGUMF5LENLIW6ZAQ',
        'SellerId' => 'A3LMXTNFZ71A3Q',
        'MarketplaceId.Id.1' => 'ATVPDKIKX0DER',
        'CreatedAfter' => '2012-08-25 00:00:00',
        'OrderStatus.Status.1' => 'Unshipped',
        'OrderStatus.Status.2' => 'PartiallyShipped',
        'Key' => 'jRa5CBIrZVTMm+GD9wwSNSQ+vwpyflw1eUn6aebL',
        ];

    try {
        $orders = $orderSpider->getOrders($option);
    
        foreach ($orders as $order) {

            $order_id  = DB::table('orders')->where('entry_id', '=', $order['entry_id'])->only('id');
            if ( !$order_id ) {
                $order_id = DB::table('orders')->insert_get_id($order);
            } else { // update
                DB::table('orders')->where('id', '=', $order_id)->update($order);
            }

            $option = [
                'AWSAccessKeyId' => 'AKIAJGUMF5LENLIW6ZAQ',
                'SellerId' => 'A3LMXTNFZ71A3Q',
                'AmazonOrderId' => $order['entry_id'],
                'Key' => 'jRa5CBIrZVTMm+GD9wwSNSQ+vwpyflw1eUn6aebL'
                ];

            try {
                $item = $orderSpider->getItems( $option );

                //foreach ($items as $item) {
                    $item['order_id'] = $order_id;

                    $item_id = DB::table('items')->where('entry_id', '=', $item['entry_id'])->only('id');
                    if ( !$item_id ) {
                        DB::table('items')->insert($item);
                    } else {
                        DB::table('items')->where('id', '=', $item_id)->update($item);
                    }
                //}
            } catch (CurlException $e) {
            
            } catch (AmazonException $e) {
            
            }
            
        }

    } catch (CurlException $e) {
        // log
        echo 'Curl Error:<hr>';
        echo $e->getError();
    
    } catch (AmazonException $e) {
        // log
        echo 'Amazon API Error:<hr>';
        echo $e->getError();
        /*
        // LOG
        $filename = '/var/www/new/api.log';
        $contents = $e->getMessage();
        if (is_writable($filename)) {
            $handle = fopen($filename, 'w+');
            fwrite($handle, $contents);
            fclose($handle);
        } else {
            exit('API log file unable to write.');
        }
         */
    }

    return ;
});

Route::controller('user');
Route::controller('order');
Route::controller('item');
Route::controller('sku_map');

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
    return Response::error('404');
});

Event::listen('500', function()
{
    return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|       Route::filter('filter', function()
|       {
|           return 'Filtered!';
|       });
|
| Next, attach the filter to a route:
|
|       Router::register('GET /', array('before' => 'filter', function()
|       {
|           return 'Hello World!';
|       }));
|
*/

Route::filter('before', function()
{
    // Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
    // Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
    if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
    if (Auth::guest()) return Redirect::to('login');
});
