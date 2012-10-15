<?php


class Stock_Controller extends Base_Controller {

    public function action_index() {
        $user_id = Sentry::user()->get('id');

        $user_platforms = User::getPlatforms( $user_id );
        $stock_datas = [];
        $platforms = [];
        foreach($user_platforms as $user_platform){
            $stock = Stock::getStock($user_id, $user_platform->id);

            $stock_datas[$user_platform->name] = $stock;
            $platforms[] = $user_platform->name;
        }


        return View::make('stock.index')->with('stocks', $stock_datas)
                                        ->with('platforms', $platforms);
    
    }
}
?>
