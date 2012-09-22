<?php

class Order_Ajax_Controller extends Base_Controller {

    // 获取订单详情
    public function action_info() {
        if( !Request::ajax() ) {
            return Response::error('404');
        }

        $order_id = Input::get('order_id');
        $order = Order::getOrder( $order_id );

        return Response::json($order);
        
    }

    // 添加订单标识
    public function action_addmark() {
        if( !Request::ajax() ) {
            return Response::error('404');
        }

        $data = [
            'order_id' => Input::get('order_id'),
            'mark_id'  => Input::get('mark_id')
            ];

        Mark::saveOrderMark( $data );

        return Response::json('ok');
    }

    // 删除订单标识
    public function action_delmark() {
        if( !Request::ajax() ) {
            return Response::error('404');
        }

            $order_id = Input::get('order_id');
            $mark_id  = Input::get('mark_id');
            $user_id  = 1;   // 当前用户
        
        Mark::delOrderMark( $order_id, $mark_id );

        return Response::json('ok');
    }

    // 批量标识订单
    public function action_setmarks() {
        if( !Request::ajax() ) {
            return Response::error('404');
        }


        $order_ids = Input::get('order_ids');
        $mark_ids = Input::get('mark_ids');
        
        // 验证

        //入库
        Order::setMarks($mark_ids, $order_ids);

        return Response::json('ok');
    }

    // 确认订单提交
    public function action_confirm() {
    
        $result = 'ok';
    
        $user_platforms = User::getPlatforms(1);

        $result = Order::confirmOrders( $user_platforms );

        return Response::json( $result );
    }

    // 获取已匹配物流的订单
    public function action_matched() {

        // 系统的运输方式
        $logistic_company = Config::get('application.logistic_company');
        $logistics = [];
        foreach ($logistic_company as $company => $value) {
            $logistics[] = [
                'name' => $company,
                'method' => $value['method'],
                ];
        }

        $result = [
            'status'  => 'success',
            'message' => [],
            'logistic' => $logistics,
            ];

        // 翻页参数
        $options = Input::get('option');
        // 已经匹配物流的订单
        $options['order_status'] = 1;

        $orders = Order::getOrders( 1, $options );

        $result['message'] = $orders->results;

        return Response::json( $result );
    }
}
?>
