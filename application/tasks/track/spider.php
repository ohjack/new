<?php
/*
 * 
 * 抓取物流状态
 * 
 */
class Task_Track_Spider{
	function __construct()
	{
		$mark=true;
		do{
			$mark=Track::runTrack();
		}while($mark);
		echo 'Finished';		
	}
}
?>