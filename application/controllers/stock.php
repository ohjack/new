<?php
/**
 * 库存信息
 */
class Stock_Controller extends Base_Controller {

    public function action_index() {
        $user_id = Sentry::user()->get('id');
        $user_platforms = User::getPlatforms( $user_id );

        return View::make('stock.index')->with('platforms', $user_platforms);
    }

    /**
     * 仓储情况列表
     */
    public function action_list() {
        $user_id = Sentry::user()->get('id');

        $platform_id = Input::get('platform_id', 0);

        $stocks = Stock::ajaxList($user_id, $platform_id);

        $data = Datatables::of($stocks)->make();
        
        return Response::json( $data );
    }
}
?>
