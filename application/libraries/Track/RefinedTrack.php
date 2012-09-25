<?php
//实现跟踪类
class Track_RefinedTrack extends Track{

	protected $_imp;
	public function __construct(Track_Iapi $iapi)
	{
		$this->_imp=$iapi;
	}

	public function get()
	{
		return $this->_imp->getData();
	}
}







?>