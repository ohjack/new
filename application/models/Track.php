<?php

class Track
{
	/*
	 * 从发货表复制物流信息
	 * 
	 * @param:$start_id		integer		开始获取ID
	 * 
	 * return:$end_id		integer		获取的结束ID	
	 */
	public static  function copy($start_id)
	{
		$end_id=0;
		$lists=DB::table('shipped')
			->where('id','>',$start_id)
			->get(array('id','tracking_no','company','method'));
		if(!empty($lists))
		{
			foreach ($lists as $listObj)
			{
				$list=(array)$listObj;
				$end_id=($list['id']>$end_id)?$list['id']:$end_id;
				DB::table('track_pending')->insert_get_id($list);
			}
		}
		return $end_id;
	}
	
	/*
	 * 从数据库读取上次复制得结束ID，作为开始ID
	 * 
	 * return:$start_id		integer		上次复制的结束ID
	 */
	public static function getStartID()
	{
		return DB::table('track_mark')->where('mark','=','end_id')->only('value');
	}
	
	
	/*
	 * 标志已copy的ID
	 * 
	 * @param:$start_id		integer		已复制最大ID
	 */
	function resetStartID($start_id)
	{
		DB::table('track_mark')
			->update(array('mark'=>'start_id','value'=>$start_id));
	}
	
	/*
	 * 获取待查物流跟踪信息的列表
	 * 
	 * @param:$number		integer		条数
	 * 
	 * return:$pendingList	array		待查列表
	 */
	function getPendingList($number)
	{
		
	}
	/*
	 * 保存返回的物流信息
	 * 
	 * @param:$trackInfo	array		物流信息
	 * 
	 * ruturn:void
	 */	
	function save($trackInfo)
	{
		
	}
	
	
	/*
	 * 删待查列表的一条记录
	 * 
	 * @param:$id	integer 	编号
	 * 
	 */
	function del($id)
	{
		
	}
	
	
	/*
	 * 更新物流状态
	 * 
	 * @param:$id		integer		编号
	 * @param:$status	integer		状态
	 *
	 */
	function updateStatus($id)
	{
		
	}
}


?>