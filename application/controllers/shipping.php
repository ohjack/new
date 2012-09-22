<?php

class shipping extends Base_Controller{

	const PART_SHIPPED =2;
	const ALL_SHIPPED  =3;

	public function action_index()
	{
		$return=array();
		$logistics=Input::get('logistics');
		if(is_array($logistics))
		{
			$ids=$this->action_insert_into($logistics);
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
//更新物流信息
	function action_insert_into($logistics)
	{
		$order_status=self::ALL_SHIPPED;
		$item_status=self::ALL_SHIPPED;
		$order=array();
		foreach ($logistics as $logKey=> $logistic)
		{
			foreach ($logistic['items'] as $key => $item)
			{
				if(!empty($item['method'])&&!empty($item['company']))
				{

					$insert_item['order_id']=$logistic[$logKey];
					$insert_items['item_id']=$item[$key];
					$insert_items['method']=$item['method'];
					$insert_items['company']=$item['company'];
					$insert_items['tracking_no']=$item['tracking_no'];
					$insert_items['quantity']=$item['quantity'];
					
					//将item插入发货表
					$ids[]=Shipping::insertShipped($insert_item);
				}
				if($item['quantity']!=(empty($item['quantity_input'])?0:$item['quantity_input']))
				{
					$order_status=self::PART_SHIPPED;
					$item_status=self::PART_SHIPPED;
				}
				
				//更新items 表中item的状态
				Shipping::updateItem($item[$key],$item_status);
			}
			
			$order['id']=$logistis['order_id'];
			$order['status']=$order_status;
			
			//更新orders中order的状态
			Shipping::updateOrder($order);
		}
		return $ids;
	}

}

?>