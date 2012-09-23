<?php
/*
 * 处理发货
 */
class Shipping{
	
	const PART_SHIPPED =2;
	const ALL_SHIPPED  =3;
	const CONFIRM_FIRST =4;
	
	/*
	 * 插入已发货信息
	 * 
	 * @
	 */
 	public static function insertShipped($Item)
	{		
		return DB::table('shipping')->insert_get_id($Item);
	}
	
	/*
	 * 更新Orders表
	 */
	public static function updateOrder($order)
	{
		return DB::table('orders')->update($order);
	}
	
	/*
	 * 更新Item
	 * 
	 */
	public static function updateItem($item)
	{
		return DB::table('items')->update($item);
	}
	
	/*
	 * 手动录入发货信息
	 * 
	 */
	public static function handleInsert($logistics)
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
					$ids[]=static::insertShipped($insert_item);
				}
				if($item['quantity']!=(empty($item['quantity_input'])?0:$item['quantity_input']))
				{
					$order_status=self::PART_SHIPPED;
					$item_status=self::PART_SHIPPED;
				}
		
				//更新items 表中item的状态
				static::updateItem($item[$key],$item_status);
			}
				
			$order['id']=$logistis['order_id'];
			$order['status']=$logistic['confirm_first']?self::CONFIRM_FIRST:$order_status;
				
			//更新orders中order的状态
			static::updateOrder($order);
		}
		return $ids;
		
		
	}
}