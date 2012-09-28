<?php

class Order_Ajax_Controller extends Base_Controller {

    const METCHED_SHIPMENT = 1;

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

    // 获取已匹配物流的订单列表
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

        // 初始化返回
        $result = [
            'status'  => 'success',
            'message' => [],
            'logistic' => $logistics,
            ];

        $options = [
            'entry_id' => Input::get('entry_id'),
            'order_status'  => self::METCHED_SHIPMENT,   
            ];

        $orders = Order::getOrders( 5, $options );


        return Response::json( $result );
    }

    // 导入发货信息
    public function action_import_logistic() {
        $result = [
            'error'=> '',
            'msg'  => ''
            ];

        $data['upload_file'] = Input::file('import_file');

        // 验证
        $rules = [
            'upload_file' => 'mimes:xls,xlsx'
            ];
        $validation = Validator::make($data, $rules);

        if( !$validation->fails() ) {
            $fileinfo = pathinfo($data['upload_file']['name']);
            $filepath = path('public') . 'data/upload/xls/';
            $filename = md5(time().rand(0,1000)).'.'.$fileinfo['extension'];
            $success = Input::upload('import_file', $filepath, $filename);
            if($success) {


                $result['msg'] = '导入成功!';
            }
        
        } else {
            $result['error'] = '请上传正确的文件。';
        }

        // 插件输出方式
        echo json_encode($result);
        //return Response::json( $result );
    }
}
?>
