<?php
//实现快递100API
class Track_kuaidi extends Track_Iapi
{
	private $_key='ac4bffc51dce962e';
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
		$query='http://api.kuaidi100.com/api?id='.$this->_key.'&com='.$this->_expressID.'&nu='.$this->_number.'&show=0&muti=1&order=asc';
		return $this->format($this->get($query));
	}
	protected  function format($json)
	{

		$temp_array=json_decode($json,true);
		$this->_data['status']=$temp_array['state'];
		$this->_data['data']=$temp_array['data'];
		return $this->_data;
	}
}
?>