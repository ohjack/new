<?php

class Shipping_Controller extends Base_Controller{



	public function action_index()
	{
		$return=array();
		$logistics=Input::get('logistics');
		if(is_array($logistics))
		{
			$ids=Shipping::handleInsert($logistics);
			if(!empty($ids))
			{
				response::json($ids);
			}
			else
			{
				$return['status']='no_insert';
				$return['massage']='没有插入任何信息';
				response::json($return);
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