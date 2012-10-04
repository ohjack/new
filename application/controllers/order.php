<?php

class Order_Controller extends Base_Controller {


    const HAD_MATCH_ORDER = 1;
    const PART_SEND_ORDER = 2; // 部分发货
    const ALL_SEND_ORDER  = 3; // 全部发货
    const MARK_SEND_ORDER = 4; // 先标记发货
    
    // 订单列表
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
        $user_id=Sentry::user()->get('id');
        // 载入用户mark
        $marks = Mark::getByUserId( $user_id );

        // view
        return View::make('order.list')->with('orders', $orders)
                                       ->with('marks', $marks)
                                       ->with('options', $options)
                                       ->with('title', '订单列表');
    
    }

    // 处理订单
    public function action_center() {
        //Session::put('step', 'spiderOrder');

        /*
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
        */
        $user_id=Senty::user()->get('id');
        $options = [
            //'orders.user_id' => Sentry::user()->get('id'),
            'orders.confirm' => 0,
            'orders.order_status' => [self::PART_SEND_ORDER, self::ALL_SEND_ORDER, self::MARK_SEND_ORDER],
            ];

        $orders = Order::getOrders(1, $options);

        $total = [
            'order'  => SpiderLog::lastTotal($user_id),
            'skumap' => count(Item::getNoSkuItems($user_id)),
            'handle' => Logistics::getTotal($user_id),
            'confirm' => $orders->total,
            ];

        return View::make('order.center')->with('total', $total)
                                         ->with('title', '处理订单');
    }

    // 订单sku映射设置列表
    public function action_skumap() {
        $user_id=Sentry::user()->get('id');
        $items = Item::getNoSkuItems($user_id);

        return View::make('order.skumap.list')->with('items', $items)
                                        ->with('title', '产品设置');
    
    }

    // 保存sku映射
    public function action_doskumap() {
    
        $datas = Input::get();

        // validation
        $rules = [
            'original_sku' => 'required|min:1',
            'target_sku'   => 'required|min:1',
            'logistics'    => 'required|min:1'
            ];
        $user_id=Sentry::user()->get('id');
        if( isset( $datas['original_sku'] ) ) {
            foreach ($datas['original_sku'] as $key => $value) {
                $data = [
                    'product_name'  => $datas['product_name'][$key],
                    'product_price' => $datas['product_price'][$key],
                    'target_sku'    => $datas['target_sku'][$key],
                    'original_sku'  => $datas['original_sku'][$key],
                    'logistics'     => $datas['logistics'][$key],
                    'user_id'       =>$user_id,
                    ];

                $validation = Validator::make($data, $rules);

                if( !$validation->fails() && !SkuMap::chkMap($data['original_sku'], $data['logistics']) ) {
                    SkuMap::saveMap($data);
                }
            }
        }

        // SKU列表
        $items = count(Item::getNoSkuItems($user_id));
        if(empty($items)) {
            if(Order::Match($user_id)) return Redirect::to('order/center');
        } else {
            return Redirect::to('order/skumap');
        }
    }

    // 物流导出
    public function action_handle() {

        $logistics = [
            'coolsystem',
            'birdsystem'
            ];
    
        $user_id = Sentry::user()->get('id');
        $files = Logistics::getXlsFile( $user_id, $logistics );

        return View::make('order.logistics.list')->with('files', $files)
                                                 ->with('title', '物流导出');
    }

    // 跟踪信息录入
    public function action_tracking() {

        // 系统的运输方式
        $logistic_company = Config::get('application.logistic_company');

        $options = [
            'entry_id' => Input::get('entry_id'),
            'order_status'  => self::HAD_MATCH_ORDER,   
            ];

        $orders = Order::getOrders( 15, $options );

        $logistic_company = Config::get('application.logistic_company');

        return View::make('order.tracking.list')->with('orders', $orders)
                                                ->with('logistic_company', $logistic_company)
                                                ->with('title', '跟踪数据录入');
    
    }

    // 确认订单
    public function action_confirm() {

        $options = [
            'orders.user_id' => Sentry::user()->get('id'),
            'orders.confirm' => 0,
            'orders.order_status' => [self::PART_SEND_ORDER, self::ALL_SEND_ORDER, self::MARK_SEND_ORDER],
            ];

        $orders = Order::getOrders(15, $options);

        return View::make('order.confirm.list')->with('orders', $orders)
                                               ->with('title', '确认订单');
    
    }

    // 执行确认订单
    public function action_doconfirm() {
        $ids = Input::get('id');
        Order::confirm( $ids );

        return Redirect::to('order/confirm');
    
    }
}
?>
