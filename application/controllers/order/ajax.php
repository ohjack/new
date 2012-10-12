<?php

class Order_Ajax_Controller extends Base_Controller {

    // 订单列表
    public function action_list() {
        $user_id = Sentry::user()->get('id');
        $orders = Order::ajaxOrders( $user_id );

        $data = Datatables::of($orders)->make();

        foreach ($data['aaData'] as $key => $order) {
            $data['aaData'][$key][5] = Config::get('application.order_status')[$order[5]]['desc'];
        }

        return Response::json( $data );
    }

    // 订单列设置
    public function action_setting() {
        $user_id = Sentry::user()->get('id');
        $fields = Input::get('fields');
        if($fields !== null) {
            Setting::setUserSetting( $user_id, 'order_list_fields', explode(',', $fields));
        }
    }

    // 跟踪信息录入
    public function action_tracking() {
        $user_id = Sentry::user()->get('id');

        $options = [
            'orders.order_status'  => HAD_MATCH_ORDER,
            ];

        $fields = [
            'order_id' => ['name' => '', 'field' => 'orders.id'],
            'order_entry_id' => ['name' => '订单ID', 'field' => 'orders.entry_id'],
            ];
    
        $orders = Order::ajaxOrders( $user_id, $fields, $options );

        $data = Datatables::of($orders)->make();

        $logistic_company = Config::get('application.logistic_company');

        $companies = array_keys($logistic_company);
        $companies = array_combine($companies, $companies);
        $company_info = Form::select('{company}', $companies);

        foreach ($data['aaData'] as $key => $order) {

            $order_id = $data['aaData'][$key][0];
            array_shift($data['aaData'][$key]);
            $data['aaData'][$key][1] = str_replace('{company}', 'logistic[' . $order_id . '][company]', $company_info);
            $data['aaData'][$key][2] = Form::text('logistic[' . $order_id . '][method]');
            $data['aaData'][$key][3] = Form::text('logistic[' . $order_id . '][tracking_no]');
        }

        return Response::json( $data );
    }

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

        Mark::save( $data );

        return Response::json('ok');
    }

    // 删除订单标识
    public function action_delmark() {
        if( !Request::ajax() ) {
            return Response::error('404');
        }

        $order_id = Input::get('order_id');
        $mark_id  = Input::get('mark_id');
        $user_id  = Sentry::user()->get('id');   // 当前用户
        
        Mark::delete( $order_id, $mark_id );

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
        //        if( !$validation->fails() ) {
        $fileinfo = pathinfo($data['upload_file']['name']);
        $filepath = path('public') . 'data/upload/xls/';
        $filename = md5(time().rand(0,1000)).'.'.$fileinfo['extension'];
        $success = Input::upload('import_file', $filepath, $filename);
        if($success) {

            Track::toDatabase($filepath.$filename);
            $result['msg'] = '导入成功!';
            /*            }

            } else {
            $result['error'] = '请上传正确的文件。';
            }
            */
            // 插件输出方式
            echo json_encode($result);
            //return Response::json( $result );
        }
    }
}
?>
