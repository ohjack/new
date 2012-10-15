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

Route::group(array('before' => 'sentry'), function(){

    Route::get('/', function() {

        $user_id = Sentry::user()->get('id');

        $order_list_columns = Setting::getUserSetting($user_id, 'order_list_fields');

        $options = [
            'orders.user_id' => $user_id,
            'orders.order_status' => [ PART_SEND_ORDER, ALL_SEND_ORDER, MARK_SEND_ORDER ],
            ];

        $orders = Order::getOrders(1, $options);

        $total = [
            'order'     => SpiderLog::lastTotal( $user_id ),
            'skumap'    => count(Item::getNoSkuItems( $user_id )),
            'handle'    => Logistics::total( $user_id ),
            'logistics' => Order::totalInputLogistic( $user_id ),
            ];

        return View::make('dashboard')->with('order_list_columns', $order_list_columns)
                                      ->with('total', $total);
    });

    Route::controller('user');
    Route::controller('spider.order');
    Route::controller('spider.item');
    Route::controller('order.logistics');
    Route::controller('order.ajax');
    Route::controller('order');
    Route::controller('item');

    Route::controller('skumap.manage');
    Route::controller('skumap');
    Route::controller('shipping');
    Route::controller('track');
    Route::controller('mark');

});
Route::controller('login');
Route::controller('register');
Route::controller('logout');





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

Route::filter('sentry',function()
{
    if(!Sentry::check()) return Redirect::to('login');

});

