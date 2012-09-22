<?php

class Step {
    
    // 重置处理订单流程
    public static function reset() {
        $current_step = Session::get('step', 'spiderOrder');
        foreach(Config::get('application.steps') as $step => $item) {
            if($current_step != $step) {
                $new_item = [
                    'name'  => $item['name'],
                    'link'  => 'javascript:;',
                    'class' => 'cantclick',
                    'id'    => $item['id'],
                    ];
            } else {
                $new_item = $item;
            }

            $new_steps[$step] = $new_item;
        }
        Config::set('application.steps', $new_steps);
        
    }

}
?>
