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


?>