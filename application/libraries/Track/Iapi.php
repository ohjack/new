<?php
//抽象物流跟踪API
abstract class Track_Iapi
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
?>