<?php
//实现ickd.cn  API
class Track_Icha extends Track_Iapi
{
	private $_key='E8B5D6EB0AF878CD25B2F4676E66B18D';
	private $_expressID;
	private $_number;
	protected $_data=array(
			'status'=>'',
			'data'=>'',
	);
	function __construct($order)
	{
		$this->_expressID=$order['expressID'];
		$this->_number=$order['number'];
	}

	public function getData()
	{
		$query="http://api.ickd.cn/?com=$this->_expressID&nu=$this->_number&id=$this->_key&type=json&encode=utf8";
		return $this->format($this->get($query));
	}
	protected  function format($json)
	{
		$temp_array=json_decode($json,true);
		print_r($temp_array);
		switch ($temp_array['status'])
		{
			case 0:
				$this->_data['status']=-1;
				break;
			case 1:
				$this->_data['status']=0;
				break;
			case 2:
				$this->_data['status']=1;
				break;
			case 3:
				$this->_data['status']=3;
				break;
			case 4:
				$this->_data['status']=4;
				break;
			default :
				$this->_data['status']=-1;
				break;
		};

		$this->_data['data']=$temp_array['data'];
		return $this->_data;

	}
}
?>

