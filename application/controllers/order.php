<?php

class Order_Controller extends Base_Controller {


    public function action_index() {

        // 搜索字段
        $options = [
            'entry_id'     => Input::get('order_id'),
            'mark_id'      => Input::get('mark_id'),
            'logistics'    => Input::get('logistics'),
            'order_status' => Input::get('order_status'),
            ];

        $orders = Order::getOrders(15, $options);

        $logistics = array_keys(Config::get('application.logistics'));

        // 载入用户mark
        $marks = Mark::getByUserId( 1 );

        // view
        return View::make('order.list')->with('orders', $orders)
                                       ->with('marks', $marks)
                                       ->with('options', $options)
                                       ->with('title', '订单列表');
    
    }

    // 处理订单
    public function action_handle() {
        //Session::put('step', 'spiderOrder');

        $step = Session::get('step');
        if($step == 'mapSetting') {
            if( !count(Item::getNoSkuItems()) ) {
                $step = 'matchLogistics';
                Session::put('step', $step); 
            }
        } 
        
        if( $step == 'handleLogistics') {
            if( !Logistics::getTotal() )
                $step = 'spiderOrder';
                Session::put('step', $step); 
        }

        Step::reset();

        return View::make('order.handle')->with('title', '处理订单');
    }

}
?>
