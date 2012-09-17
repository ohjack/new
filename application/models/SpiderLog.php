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
     * 获取上上次抓取时间
     *
     * 每次抓取都从上上次的时间到当前时间段进行抓取
     * 为了防止漏抓,每段时间的订单都会抓取两次
     * spider表中lasttime就是上上次抓取时间,prevtime是上次抓取时间
     * 如果抓取时间短与2分钟返回空
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
     * 将上次抓取时间更新成上上次更新时间放在lasttime字段
     * 将当前时间储存在上次抓取时间prevtime字段
     *
     * @param: $type        integer 类型
     * @param: $platform_id integer 平台ID
     *
     * return void
     */
    public static function updateLastSpider( $type, $platform_id ) {

        $prevtime = DB::table('spider_log')->where('type', '=', $type)
                                           ->where('platform_id', '=', $platform_id)
                                           ->only('prevtime');

        $data = [
            'lasttime' => $prevtime,
            'prevtime' => date('Y-m-d H:i:s')
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
            'prevtime'    => $datetime,
            'lasttime'    => $datetime
            ];

        DB::table('spider_log')->insert($data);

        return $datetime;
    
    }
}
?>
