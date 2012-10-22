<?php

class Track
{
    const PENDING_ORDER   = 0; // 待处理
    const HAD_MATCH_ORDER = 1; // 已分配物流
    const PART_SEND_ORDER = 2; // 部分发货
    const ALL_SEND_ORDER  = 3; // 全部发货
    const MARK_SEND_ORDER = 4; // 先标记发货
    /*
     * 从发货表复制物流信息
    *
    * @param:$start_id      integer     开始获取ID
    *
    * return:$end_id        integer     获取的结束ID
    */
    public static  function copy($start_id)
    {
        $end_id=$start_id;
        $lists=DB::table('shipped')
        ->where('id','>',$start_id)
        ->or_where_not_in('tracking_no',array('Latter','latter','later'))
        ->get(array('id','tracking_no','company','method'));
        if(!empty($lists))
        {
            print_r($lists);
            foreach ($lists as $listObj)
            {
                $list=(array)$listObj;
                $end_id=($list['id']>$end_id)?$list['id']:$end_id;
                DB::table('track_pending')->insert_get_id($list);
            }
        }
        return $end_id;
    }

    /*
     * 从数据库读取上次复制得结束ID，作为开始ID
    *
    * return:$start_id      integer     上次复制的结束ID
    */
    public static function getStartID()
    {
        return DB::table('track_mark')->where('mark','=','start_id')->only('value');
    }


    /*
     * 标志已copy的ID
    *
    * @param:$start_id      integer     已复制最大ID
    */
    public static function resetStartID($start_id)
    {
        DB::table('track_mark')
        ->where('mark','=','start_id')
        ->update(array('value'=>$start_id));
    }

    /*
     * 获取待查物流跟踪信息的列表
    *
    * @param:$number        integer     条数
    *
    * return:$pendingList   array       待查列表
    */
    public static function getPendingList($number)
    {
        return DB::table('track_pending')->take($number)->get();
    }
    /*
     * 保存返回的物流信息
    *
    * @param:$trackInfo array       物流信息
    *
    * ruturn:void
    */
    public static function save($trackInfo)
    {
        DB::table('track_finished')->insert_get_id($trackInfo);
    }


    /*
     * 删待查列表的一条记录
    *
    * @param:$id    integer     编号
    *
    */
    public static function del($id)
    {
        DB::table('track_pending')->delete($id);
    }


    /*
     * 更新物流状态
    *
    * @param:$id        integer     编号
    * @param:$status    integer     状态
    *
    */
    public static function updateStatus($id,$status)
    {
        DB::table('track_pending')
        ->where('id','=',$id)
        ->update(array('status'=>$status,'tracked_at'=>date("Y-m-d H:i:s")));
    }

    /*
     * 抓取物流跟踪信息
    *
    */
    public static function runTrack()
    {

        $pendinglist=self::getPendingList(1);
        if(empty($pendinglist))
        {
            return false;
        }
        $count=0;
        foreach($pendinglist as $listObj)
        {
            $count+=1;
            echo '>';
            $list=(array)$listObj;
            $trackMap=array(
                    array('kuaidi',array('dhl','ups','usps','fedex','tnt')),
                    array('icha',array('ems','shunfeng')),
                    array('royalmail',array('royalmail')),
            );
            foreach ($trackMap as $i)
            {
                if(in_array(strtolower($list['company']), $i[1]))
                {
                    $var=$i[0];
                }
            }
            if(empty($var))
            {
                return false;
            }
            if(!empty($var))
            {

                $TrackObj['expressID']=$list['company'];
                $TrackObj['number']=$list['tracking_no'];
                $var='Track_'.ucfirst($var);

                //$track=new RefinedTrack(new kuaidi($order));
                $track=new Track_RefinedTrack(new $var($TrackObj));
                $data=$track->get();
                $data['company']=$list['company'];
                $data['tracking_no']=$list['tracking_no'];
                $data['data']=serialize($data['data']);
                if(!empty($data['status']))
                {
                    if($data['status']==3||$data['status']==10)
                    {

                        self::save($data);

                        self::del($list['id']);
                    }
                    else
                    {
                        self::updateStatus($list['id'],$data['status']);
                    }
                }
            }
        }

        return true;
    }


    /*
     * 复制发货表
    *
    */
    public static function runCopy()
    {
        $start_id=self::getStartID();
        $end_id=self::copy($start_id);
        self::resetStartID($end_id);
    }
    
    
    /*
     * 
     * read xls to database
     * 
     * @param    $path    xls文件路径            
     * 
     */
    public static function toDatabase($path)
    {
        $filename=$path;
        $PHPExcel=new PHPExcel();
        $PHPRead=new PHPExcel_Reader_Excel2007();
        if(!$PHPRead->canRead($filename))
        {
            $PHPRead=new PHPExcel_Reader_Excel5();
            if(!$PHPRead->canRead($filename))
            {
                exit();
            }
        }
        $PHPExcel=$PHPRead->load($filename);
        $arr=$PHPExcel->getSheet(0)->toArray();
        foreach($arr as $key=>$shipp)
        {
            if(!$key=='0'){
                
                $orderInfo=Shipping::getOrder('entry_id', $shipp[0]);
                $temp_id=empty($orderInfo)?0:$orderInfo->id;
                if($temp_id!=0)
                {

                    $input=array(
                            'entry_id'=>$shipp[0],
                            'item_id'=>empty($shipp[1])?0:$shipp[1],
                            'quantity'=>empty($shipp[2])?0:$shipp[2],
                            'company'=>$shipp[3],
                            'tracking_no'=>$shipp[4],
                            'method'=>empty($shipp[5])?0:$shipp[5],                         
                            'order_id'=>$temp_id,
                            'created_at'=>date("Y-m-d H:i:s"),
                    );
                    //print_r($input);
                    DB::table('shipped')->insert_get_id($input);
                    
                    //更新订单状态
                    Shipping::updateOrder($temp_id,array('order_status'=>self::ALL_SEND_ORDER));
                }
            }
        }
         
    }
    
    /*读取跟踪信息
     * 
     * @param    $order_id    订单编号
     * 
     * return
     */
    public static function getTracking($order_id)
    {
        return DB::table('shipped') ->where('shipped.order_id','=',$order_id)
                                    ->left_join('track_finished','shipped.tracking_no','=','track_finished.tracking_no')
                                    ->get(array('shipped.order_id','shipped.company','shipped.method','shipped.tracking_no','track_finished.status','track_finished.data'));
    }
}


?>
