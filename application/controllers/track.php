<?php
class  Track_controller extends Base_Controller
{
	public function action_index()
	{
        
	}
	public function action_copy()
	{
		Track::runCopy();
	}
	public function action_track()
	{
		Track::runTrack();
	}
	public function action_readxls()
	{
		Track::runReadXls();
	}
}

?>