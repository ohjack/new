<?php
/*
 * 处理发货
 */
class Shipping{
	/*
	 * 插入已发货信息
	 */
 	public static function insertShipped($Item)
	{		
		return DB::table('shipping')->insert_get_id($Item);
	}
	public static function updateOrder($order)
	{
		return DB::table('orders')->update($order);
	}
	public static function updateItem($item)
	{
		return DB::table('items')->update($item);
	}
}