<?php
use Laravel\Redirect;

class Mark_Controller extends Base_Controller
{
    public function action_index()
    {
        $user_id=Sentry::user()->get('id');
        $marks=Mark::getByUserId($user_id);
        return View::make('mark')
                        ->with('marks',$marks)
                        ->with('title','标识设置');
    }
   
    public function action_delete()
    {
        $mark_ids=Input::get('mark_ids');
        $mark_ids=explode(',',$mark_ids);
        Mark::delFromMarkTable($mark_ids);
        return Redirect::to('mark');
    }
    public function action_submit()
    {
        $marks=Input::get('mark');
        $adds=Input::get('add');
        $user_id=Sentry::user()->get('id');
        foreach($adds as $key=>$add)
        {
            if(!empty($add['name']))
            {
                $data=array(
                        'user_id'=>$user_id,
                        'name'=>$add['name'],
                        'color'=>$add['color'],
                        'ico'=>$add['ico'], 
                        'sort'=>$add['sort']
                );
                Mark::add($data);
            }
        }
        //print_r($marks);die;
        foreach($marks as $key=>$mark)
        {
            //echo count($mark);die;
            if(!empty($mark['name']))
            {
                $data=array(
                        'name'=>$mark['name'],
                        'color'=>$mark['color'],
                        'ico'=>$mark['ico'],
                        'sort'=>$mark['sort']
                );
                Mark::update($mark['mark_id'],$data);
            }
        }
        return Redirect::to('mark');
    }
}
?>