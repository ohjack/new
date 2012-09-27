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
			case 'copy':
				new Task_Track_Copy();
				break;
			case 'spider':
				new Task_Track_Spider();
				break;
			case 'test':
				new Task_Track_Test();
				break;
			default :
				$this->_help();
				break;
		}
		
	}
	
	//帮助
	private function _help()
	{
		echo "help list";
	}
}
?>