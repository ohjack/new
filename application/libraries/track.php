<?php

//抽象物流跟踪类
abstract class Track
{

	protected $_imp;

	public function get()
	{
		return $this->_imp->getData();
	}
}

//实现跟踪类
class RefinedTrack extends Track{

	protected $_imp;
	public function __construct(Iapi $iapi)
	{
		$this->_imp=$iapi;
	}

	public function get()
	{
		return $this->_imp->getData();
	}
}


//抽象物流跟踪API
abstract class Iapi
{
	abstract public function getData();

	/*
	 * 格式化获取得到的跟踪信息
	 */
	abstract protected  function format($json);
	
	protected $_data=array(
			'status'=>'',
			'data'=>'',
	);
	public function get($url)
	{
		if (function_exists('curl_init') == 1){
			$curl = curl_init();
			curl_setopt ($curl, CURLOPT_URL, $url);
			curl_setopt ($curl, CURLOPT_HEADER,0);
			curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
			curl_setopt ($curl, CURLOPT_TIMEOUT,5);
			$get_content = curl_exec($curl);
			curl_close ($curl);

		}else{

			include("snoopy.php");
			$snoopy = new snoopy();
			$snoopy->referer = 'http://www.google.com/';
			$snoopy->fetch($url);
			$get_content = $snoopy->results;
		}

		return $get_content;
	}
}


//实现快递100API
class kuaidi extends Iapi
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


//实现ickd.cn  API
class icha extends Iapi
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


//实现rayalmail 获取物流跟踪信息
class royalmail extends Iapi
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
		echo $query;
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