<?php
/*
 * 处理发货
*/
class Shipping{

	const PART_SEND_ORDER=2;
	const ALL_SEND_ORDER  =3;
	const MARK_SEND_ORDER =4;

	/*
	 * 插入已发货信息
	*
	* @param:$Item	array	商品的物流信息
	* 
	* return:数据库行号
	*/
	public static function insertShipped($Item)
	{
		return DB::table('shipped')->insert_get_id($Item);
	}

	/*
	 * 更新Orders表
	 * 
	 * @param:$order_id integer 订单编号
	 * @param:$arr 		array	订单信息
	 * 
	 * return:
	*/
	public static function updateOrder($order_id,$arr)
	{
		return DB::table('orders')->where('id','=',$order_id)->update($arr);
	}

	/*
	 * 更新Item
	 * 
	 * @param:$item_id		integer		商品编号
	 * @param:$item_status	integer		发货状态
	*
	*/
	public static function updateItem($item_id,$status)
	{
		$item_ar=array('status'=>$status);
		return DB::table('items')->where('id','=',$item_id)->update($item_ar);
	}
	
	/*
	 * 物流跟踪号是否已存在shipped表中
	 * 
	 * @param:the number of the tracking infomaction
	 */
	public static function existTrackInfo($tracking_no)
	{
		return DB::table('shipped')->where('tracking_no','=',$tracking_no)->only('id');
	}
	
	
	/*
	 * 手动录入发货信息
	*
	*@param:物流信息数组
	*/
	public static function handleInsert($logistics)
	{
		$order_status=self::ALL_SEND_ORDER;
		$item_status=self::ALL_SEND_ORDER;
		$order=array();
		$quantity_match_number=0;
		$unset_item_number=0;
		$insert_item=array();

		foreach ($logistics as $logKey=> $logistic)
		{
			//进入循环前先重置统计数
			$quantiy_match_number=0;
			$unset_item_number=0;
			$insert_item=null;
			foreach ($logistic['items'] as $key => $item)
			{
				if($item['quantity']==$item['ship_quantity'])
				{
					$quantity_match_number+=1;
				}
				else
				{
					//部分发货
					$order['order_status']=self::PART_SEND_ORDER;
					$item_status=self::PART_SEND_ORDER;
				}
				if(!empty($item['method'])&&!empty($item['company']))
				{
					$insert_item['order_id']=$logKey;
					$insert_item['item_id']=$key;
					$insert_item['method']=$item['method'];
					$insert_item['company']=$item['company'];
					$insert_item['tracking_no']=$item['tracking_no'];
					$insert_item['quantity']=$item['ship_quantity'];
					$orderInfo=self::getOrder('order_id', $logKey);
					$insert_item['entry_id']=$orderInfo[0]->entry_id;

					//将item插入发货表
					$ids[]=static::insertShipped($insert_item);
					//更新items 表中item的状态
					static::updateItem($key,$item_status);
					if(empty($logistic['method']))
					{
						$logistic['method']=$item['method'];
					}
					if(empty($logistic['company']))
					{
						$logistic['company']=$item['company'];
					}
				}
				else
				{
					$unset_item_number+=1;
				}

			}
			if($quantity_match_number==count($logistic['items']))
			{
				$order['order_status']=self::ALL_SEND_ORDER;
			}
			if($unset_item_number==count($logistic['items']))
			{
				if(!empty($logistic['method'])&&!empty($logistic['company']))
				{
					foreach ($logistic['items'] as $key => $item)
					{
						$insert_item['order_id']=$logKey;
						$insert_item['item_id']=$key;
						$insert_item['method']=$logistic['method'];
						$insert_item['company']=$logistic['company'];
						$insert_item['tracking_no']=$logistic['tracking_no'];
						$insert_item['quantity']=!empty($item['quantity'])?$item['quantity']:0;
						$insert_item['created_at']=date("Y-m-d H:i:s");
						$orderInfo=self::getOrder('id', $logKey);
						$insert_item['entry_id']=$orderInfo->entry_id;
						//if(!self::existTrackInfo($insert_item['tracking_no']))
						//{
							static::insertShipped($insert_item);
						//}
						//更新items 表中item的状态
						static::updateItem($key,$item_status);
					}
				}
			}
			$order['order_status']=!empty($logistic['ship_first'])?self::MARK_SEND_ORDER:$order['order_status'];
			if(!empty($logistic['method'])&&!empty($logistic['company']))
			{
				//更新orders中order的状态
				static::updateOrder($logKey,$order);
			}
		}
		return 'done';


	}
	
	/*
	 * 获取订单信息
	 * 
	 * @param    $column    列名
	 * @param    $value     列值
	 * 
	 * return    
	 */
	public static function getOrder($column,$value)
	{
        return DB::table('orders')->where($column,'=',$value)
                                 ->first();   
	}
}