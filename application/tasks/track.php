<?php
class Track_Task extends Task
{
	//运行入口
	public function run($arguments=[])
	{
		if(!count($arguments)) $this->_help();
		
		$command=($arguments[0] !=='')?$arguments[0]:'help';
		$args=array_slice($arguments,1);
		switch($command)
		{
			case 'c':
			case 'copy':
				new Task_Track_Copy();
				break;
			case 's':	
			case 'spider':
				new Task_Track_Spider();
				break;
			default :
				$this->_help();
				break;
		}
		
	}
	
	//帮助
	private function _help()
	{
        echo '帮助 ：';
        echo "\ttrack <命令> [参数] [选项 ..]\n";
        echo "命令：\n";
        echo "\tc/copy\t复制已发货数据到待查列表\n";
        echo "\ts/spider\t抓取物流信息\n";
		exit();
	}
}
?>