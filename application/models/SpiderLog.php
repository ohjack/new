<?php
/**
 * 平台抓取日志
 *
 * 为下次抓取提供参照物
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:SpiderLog.php  2012年09月10日 星期一 16时42分25秒Z $
 */

class SpiderLog {

    /**
     * 获取上次抓取时间
     *
     * @param: $type         string  抓取类型
     * @param: $flatform_id  integer 用户平台ID
     *
     * return dateline or false 最后抓取时间
     */
    public static function getLastSpider( $type, $platform_id ) {

        $lasttime = DB::table('spider_log')->where('platform_id', '=', $platform_id)
                                           ->where('type', '=', $type)
                                           ->only('lasttime');

        // 如果为空则新增
        if(empty($lasttime)) $lasttime = static::insertLastSpider( $type, $platform_id );

        return time() - strtotime($lasttime) < 120 ? false : $lasttime;
    
    }

    /**
     * 更新抓取时间
     *
     * @param: $type        integer 类型
     * @param: $platform_id integer 平台ID
     * @param: $total       integer 抓取数目
     *
     * return void
     */
    public static function updateLastSpider( $type, $platform_id, $total ) {

        $data = [
            'lasttime' => date('Y-m-d H:i:s'),
            'total'    => $total
            ];

        DB::table('spider_log')->where('type', '=', $type)
                               ->where('platform_id', '=', $platform_id)
                               ->update($data);
    
    }

    /**
     * 新增抓取记录
     *
     * 记录抓取时间以便以后从这个时间段进行抓取
     *
     * @param: $type         string   抓取类型
     * @param: $platform_id  integer  平台ID
     *
     * return datetime
     */
    public static function insertLastSpider( $type, $platform_id ) {

        $datetime = date('Y-m-d' . '00:00:00');

        $data = [
            'type'        => $type,
            'platform_id' => $platform_id,
            'lasttime'    => $datetime
            ];

        DB::table('spider_log')->insert($data);

        return $datetime;
    
    }

    /**
     * 获取抓取的订单数
     *
     * @param: $user_id interge 用户ID
     *
     * return interge
     */
    public static function lastTotal( $user_id ) {
        $spider_log = DB::table('users_platform')->left_join('spider_log', 'users_platform.platform_id', '=', 'spider_log.platform_id')
                                                 ->where('users_platform.user_id', '=', $user_id)
                                                 ->order_by('spider_log.lasttime', 'DESC')
                                                 ->first();

        return $spider_log->total > 99 ? 'N' : $spider_log->total;

    }


}
?>
