<?php
//实现rayalmail 获取物流跟踪信息
class Track_Royalmail extends Track_Iapi
{
	private $_number;
	function __construct($order)
	{
		$this->_number=$order['number'];
	}
	function getData()
	{
		$query="http://www.parcelforce.com/track-trace?trackNumber=$this->_number&page_type=rml-tracking-details";
		$temp_data=$this->get($query);
//		echo $query;
		if(preg_match('/<h2>Results<\/h2>(.*?)<\/p>/is',$temp_data,$mString))
		{

			return $this->format(str_replace('<p>', '', $mString[1]));


		}

	}
	function format($str)
	{
		$this->_data['status']=10;
		$this->_data['data']=array($str);
		return $this->_data;
	}
}
?>