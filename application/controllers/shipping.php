<?php

class Shipping_Controller extends Base_Controller{

    public function action_index()
    {
        $return=array();
        $logistics=Input::get('logistic');
        if(is_array($logistics))
        {
            $reStr=Shipping::handleInsert($logistics);
            if($reStr=='done')
            {
                $return['status']='done';
                $return['massage']='';
                return Response::json($return);
            }
            else
            {
                $return['status']='no_insert';
                $return['massage']='没有插入任何信息';
                return Response::json($return);
            }
        }
        else
        {
            $return=array('status'=>'data_not_match','message'=>'数据不正确');
            return Response::json($return);
        }
    }

}

?>
