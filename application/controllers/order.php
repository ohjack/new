<?php

class Order_Controller extends Base_Controller {


    public function action_index() {

        // 搜索字段
        $options = [
            'entry_id' => Input::get('order_id'),
            'mark_id'  => Input::get('mark_id'),
            ];

        $orders = Order::getOrders(15, $options);

        $logistics = array_keys(Config::get('application.logistics'));

        foreach ($orders->results as $order) {
            if( $order->order_status == 'unhandle') {
                $order->order_status = '待处理';
            } else if($order->order_status == 'handled') {
                $order->order_status = '已经处理';
            } else if(in_array($order->order_status, $logistics)) {
                $order->order_status = '已匹配物流<br />' . $order->order_status;
            }
        }

        // 载入用户mark
        $marks = Mark::getByUserId( 1 );

        // view
        return View::make('order.list')->with('orders', $orders)
                                       ->with('marks', $marks)
                                       ->with('options', $options)
                                       ->with('title', '订单列表');
    
    }

    public function action_handle() {
        //Session::put('step', 'spiderOrder');

        $step = Session::get('step');
        if($step == 'mapSetting') {
        
            if(!count(Item::getNoSkuItems())) {
                Session::put('step', 'matchLogistics'); 
            }
        }


        return View::make('order.handle')->with('title', '处理订单');
    }
}
?>
